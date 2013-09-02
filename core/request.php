<?php

    /**
     * Request class
     * Contains the entry point and requests informations
    **/
    
    class Request {
        
        public static $method;
        public static $URI;
        public static $domain;
        private static $params       = array();
        private static $_POST        = array();
        
        
        public static $globales     = array();
        
        
        const METHOD_POST   = '_POST';
        const METHOD_GET    = '_GET';
                
        public function __construct() {
            $query          = strip_system_root($_SERVER['REQUEST_URI']);
            $static         = preg_replace('#(/[a-z0-9\.-]+)?(\?(.+))?$#iSU', "$1", $query);
            $additional     = str_replace($static, '', preg_replace('#([a-z0-9/\.-]+)?(\?(.+))$#iSU', "$3", $query));	
            
            self::$domain       = $_SERVER['HTTP_HOST'];
            self::$method       = $_SERVER['REQUEST_METHOD'];
            self::$URI      = str_replace(path(), '', path($static));
                        
            foreach(explode('&', $additional) as $i => $parameter) {
                @list($name, $value) = explode('=', $parameter);
                self::$params['_GET'][$name] = urldecode($value);
            }
        
            if(!empty($_POST)):
                self::$params['_POST'] = $_POST;
            endif;
        }
        
        public static function param($name, $method = Request::METHOD_GET) {
            return (!empty(self::$params[$method][$name]) ? self::$params[$method][$name] : null);
        }
                
        public static function assign($globale, $value) {
            self::$globals[$globale] = $value;
        }
        
        public static function pick($globale) {
            return (!empty(self::$globals[$globale]) ? self::$globals[$globale] : null);
        }

    }

?>