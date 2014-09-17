<?php

    /**
     * Define Manifest action to use and 
     * contains request informations 
     * (parameters, headers, URI, domain, ...)
     *
     * @package Core
    **/
    class Request {
        
        protected static $uri       = '';
        protected static $method    = '';
        protected static $format    = '';
        protected static $action    = '';
        
        /**
         * Build request and define current controller:action to use
        **/
        public static function init() {
            $query          = substr($_SERVER['REQUEST_URI'], strlen(SYSTEM_DIRECTORY));
            $static         = preg_replace('#([a-z0-9\.-/]+)?(\?(.+))?$#iSU', "$1", $query);
            $additional     = str_replace($static, '', preg_replace('#([a-z0-9/\.-]+)?(\?(.+))$#iSU', "$3", $query));
                        
            /**
             * Define global request informations
            **/
            self::$uri           = $static;
            self::$format        = pathinfo($static, PATHINFO_EXTENSION);
            self::$method        = mb_strtoupper($_SERVER['REQUEST_METHOD']);            
                    
            /**
             * Define request parameters
            **/
            if(!empty($_GET)): // GET parameters
                self::$parameters['GET'] = strip_magic_quotes($_GET);
            
            elseif(!empty($additional)):
                foreach(explode('&', $additional) as $i => $parameter) {
                    @list($name, $value) = explode('=', $parameter);
                    self::$parameters['GET'][$name] = urldecode($value);
                }
            
            endif;
                                    
        }     
        
        
        /**
         * Get a GET option data
         * @param string    $name       The GET item name
         * @return  mixed   The input value or null
        **/
        public static function parameter($name) {
            if(!isset($_GET[$name]))
                return null;
            
            return $_GET[$name];
        }
        
        /**
         * Get a POST input data
         * @param string    $name       The POST item name
         * @return  mixed   The input value or null
        **/
        public static function input($name = null) {
            if(self::$method == 'PUT') {
                $input = fopen("php://input", "r");                
                return (!$input ? $input : null);
            }
            else {
                return array_value($name, $_POST, null);
            }
        }
        
        /**
         * Get FILES parameter
         * @param string    $name
         * @return string
        **/
        public static function file($name) {            
            if(!isset($_FILES[$name]))
                return null;
            
            return $_FILES[$name]; 
        }
        
        /**
         * Get GET parameter
         * @param string    $name
         * @return string
        **/
        public static function get($information) {
            if(!isset(self::$$information))
                trigger_error("Undefined parameter Request::\$$information", E_USER_ERROR);
            
            return self::$$information;
        }
        
        /**
         * Get header's parameter value
         * @return mixed
        **/
        public static function header($parameter, $split = false) {
            if(!isset($_SERVER[$parameter]))
                trigger_error("Request header $parameter does not exists", E_USER_ERROR);
                
            return ($split ? explode(',', $_SERVER[$parameter]) : $_SERVER[$parameter]);     
        }
        
        /**
         * Check secured connexion
         * @return boolean
        **/
        public static function secure() {
            return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');     
        }
        
        
        /**
         * Check XML HTTP Request life
         * @return boolean
        **/
        public static function ajax() {
            return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');     
        }
        
        
        /**
         * Check CLI request life
         * @return boolean
        **/
        function cli() {
            return (!isset($_SERVER['SERVER_SOFTWARE']) && (PHP_SAPI == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)));
        }
        
        
        /**
         * Check or get method
         * @param string $verify
         * @return mixed (boolean, string)
        **/
        public static function method($verify = null) {
            if(!empty($verify)):
                return (self::$method == mb_strtoupper($verify));
            else:
                return self::$method;
            endif;
        }
        
        /**
         * Check or get format (use uri extension)
         * @param string $verify
         * @return mixed
        **/
        public static function format($verify = null) {
            if(!empty($verify)):
                return (self::$format == $verify);
            else:
                return self::$format;
            endif;
        }
        
        /**
         * Get request URI (without host)
         * @return string
        **/
        public static function uri() {
            return self::$uri;     
        }
        
        /**
         * Get current domain
         * @return string Access domain
        **/
        public static function domain() {
            return $_SERVER['HTTP_HOST']; 
        }
        
        /**
         * Get current port
         * @return integer
        **/
        public static function port() {
            return $_SERVER['SERVER_PORT'];   
        }
        
        /**
         * Get request range
        **/
        public static function range() {
            return (isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : false);   
        }

    }

?>