<?php
    /**
     * Gerencia a saida de dados para visualização
     */
    class View
    {
        private $_template;
        private $_data = array(
            'title' => '-',
            'content' => '---',
            'type' => 'string'
        );

        // NOTE: Cada model retorna seus dados de saida pela func getOutcome
        public function __construct($model, $template, $erro = false)
        {
            if ($erro !== false) {
                $this->_data['erro']=$erro;
            }
            $this->_template = $template;
            try {
                $this->_data = $model->getOutcome();
            } catch (\Throwable $e) {
                $this->_data['title'] = "Mistakes were made";
                $this->_data['content'] = "Juro que tentei, mas não te compreendi.";
            }
        }

        public function concatenar($modo = 0)
        {
            switch ($modo) {

                default:
                case 0:
                    $output['title'] = "<h1>" . $this->_data["title"] . "</h1>";
                    $output['type'] = isset($this->_data["type"])?$this->_data["type"]:gettype($this->_data["content"]);
                    switch ($output['type']) {
                        case 'default':
                        case 'string':
                            $output['content'] = "<h4>"
                                . str_replace("\n","<br>",$this->_data["content"])
                                . "</h4>";
                            break;
                        // NOTE: Lista ordenada caso output for Array
                        case 'array':
                            $output['content'] = "<ol>";
                            foreach ($this->_data['content'] as $i => $item) {
                                $output['content'] .= "<li>"
                                    . $item
                                    . "</li>";
                            }
                            $output['content'] .= '</ol>';
                            break;
                        case 'table':
                            $output['content'] = '<table class="table table-hover">';
                            foreach ($this->_data['content'] as $i => $linha) {
                                $output['content'] .= '<tr>';
                                $output['content'] .= '<td>';
                                $output['content'] .= $i+1;
                                $output['content'] .='</td>';
                                    foreach ($linha as $ii => $item) {
                                        $output['content'] .= '<td>';
                                        $output['content'] .= $item;
                                        $output['content'] .='</td>';
                                    }
                                $output['content'] .= '</tr>';
                            }
                            $output['content'] .= '</table>';
                            break;
                        // NOTE: Estrutura para video caso output for um video.
                        case 'video':
                            $output['content'] = '<video controls preload="auto" src="'. Displayy\Router::getFullOrigin($_SERVER) . DS . 'stream" width="100%"></video>';
                            break;
                        default:
                            $output['content'] = '-';
                            break;
                    }
                    # code...
                    break;
            }

            return $output;
        }

        public function render()
        {
            $templatePath = ROOT . DS . "app" . DS . "template" . DS . $this->_template . ".php";

            // NOTE: Constroi o layout atraves da array output
            $output = $this->concatenar();
            if (!empty($this->_data['erro'])) {
                $output['erro'] = $this->_data['erro'];
            }

            ob_start(array($this,'compress'));
            include ($templatePath);
            ob_end_flush();
        }

        private function compress($buffer)
        {
            $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
            $buffer = preg_replace('/<!--.*?-->/ms', '', $buffer);
            $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  '), '', $buffer);
            return $buffer;
        }

    }
