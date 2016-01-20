<?php
    class Router {

    private static $urlRoot = "http://localhost";
    private static $routes = array();

    private function __construct() {}
    private function __clone() {}

    public static function getUrlRoot()
    {
        return self::$urlRoot;
    }

    public static function getOrigin( $s, $use_forwarded_host = false )
    {
        $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
        $sp       = strtolower( $s['SERVER_PROTOCOL'] );
        $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
        $port     = $s['SERVER_PORT'];
        $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
        $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
        $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    public static function getFullOrigin( $s, $use_forwarded_host = false )
    {
        return self::getOrigin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
    }

    public static function route($pattern, $callback) {
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
        self::$routes[$pattern] = $callback;
    }

    public static function execute($url) {
        foreach (self::$routes as $pattern => $callback) {
            if (preg_match($pattern, $url, $params)) {
                array_shift($params);
                return @call_user_func_array($callback, array_values($params));
            }
        }
        throw new \Exception("Err404", 404);
    }
    }
