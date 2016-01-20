<?php
    /**
     * Controller :D
     */
    class Controller {

        protected $_model;
        protected $_action;

        function __construct($model, $action, $arguments = '') {

            $this->_action = $action;
            $this->_model = $model;
            if (!method_exists($this, strtolower($this->_action))) {
                throw new \Exception('File not found', 404);
                return false;
            }
            return true;
        }

        public function index()
        {
            return $this->_model->index();
        }
    }
