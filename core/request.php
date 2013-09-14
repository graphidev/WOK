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
                
        public function __construct() {
            $query          = strip_system_root($_SERVER['REQUEST_URI']);
            $static         = preg_replace('#(/[a-z0-9\.-]+)?(\?(.+))?$#iSU', "$1", $query);
            $additional     = str_replace($static, '', preg_replace('#([a-z0-9/\.-]+)?(\?(.+))$#iSU', "$3", $query));	
            
            self::$domain       = $_SERVER['HTTP_HOST'];
            self::$method       = strtoupper($_SERVER['REQUEST_METHOD']);
            self::$URI          = str_replace(path(), '', path($static));
                        
            foreach(explode('&', $additional) as $i => $parameter) {
                @list($name, $value) = explode('=', $parameter);
                self::$params['GET'][$name] = urldecode($value);
            }
        
            if(!empty($_POST)):
                self::$params['POST'] = $_POST;
            endif;
        }
        
        public static function param($name, $method = null) {
            if(empty($method))
                $method = Request::$method;
            
            return (!empty(self::$params[$method][$name]) ? self::$params[$method][$name] : null);
        }
               
        public static function assign($globale, $value) {
            self::$globals[$globale] = $value;
        }
        
        public static function pick($globale) {
            return (!empty(self::$globals[$globale]) ? self::$globals[$globale] : null);
        }

        
        public static function is($type) {
            if(strtoupper($type) == 'AJAX'):
                return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
            
            elseif(strtoupper($type) == self::$method):
                return true;
            
            else:
                return false;
            
            endif;
        }

    }

?>