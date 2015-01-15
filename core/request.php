<?php

    /**
     * Parse request and define informations about it.
     * Allows to get request informations anywhere.
     *
     * @package Core
    **/
    class Request {
        
        protected static $query     = '/';
        protected static $uri       = '/';
        protected static $method    = 'GET';
        protected static $format    = null;
        protected static $language  = SYSTEM_DEFAULT_LANGUAGE;
        protected static $inputs    = array();
        
        /**
         * Prevent the usage of the __construct() method
        **/
        private function __construct(){}
        
        /**
         * Build request and define current controller:action to use
        **/
        public static function parse() {
            
            /** Request parameters définition **/
            self::$query        = substr($_SERVER['REQUEST_URI'], strlen(SYSTEM_DIRECTORY));
            self::$uri          = (strpos(self::$query, '?') ? strstr(self::$query, '?', true) : self::$query);
            self::$format        = pathinfo(self::$uri, PATHINFO_EXTENSION);
            self::$method        = mb_strtoupper($_SERVER['REQUEST_METHOD']);            
            
            
            /** Language definition **/
            $languages = get_accepted_languages(explode(' ', SYSTEM_LANGUAGES));
            if(Cookie::exists('language', true) && in_array($language = Cookie::get('language'), $languages)) {
                self::$language = $language;
            }
            elseif(Session::exists('language', true) && in_array($language = Session::get('language'), $languages)) {
                self::$language = Session::get('language');   
            }
            elseif(!empty($languages)) {
                self::$language = array_shift($languages);
            }
            else {
                self::$language = SYSTEM_DEFAULT_LANGUAGE;   
            }

            Session::set('language', self::$language);
            Cookie::set('language', self::$language, 15811200);
            
            
            /** Force $_GET parameters definition **/
            if(empty($_GET) && ($parameters = substr(self::$query, strlen(self::$uri)+1))):
                foreach(explode('&', $parameters) as $i => $parameter) {
                    @list($name, $value) = explode('=', $parameter);
                    $_GET[$name] = (isset($value) ? $value : true);
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
            
            return urldecode($_GET[$name]);
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
        public static function cli() {
            return (!isset($_SERVER['SERVER_SOFTWARE']) && (PHP_SAPI == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)));
        }
        
        
        /**
         * Check or get method
         * @param string $verify
         * @return mixed (boolean, string)
        **/
        public static function method() {
            return self::$method;
        }
        
        /**
         * Check or get format (use uri extension)
         * @param string $verify
         * @return mixed
        **/
        public static function format() {
            return self::$format;
        }
        
        /**
         * Get request URI (without host)
         * @return string
        **/
        public static function uri() {
            return self::$uri;     
        }
        
        /**
         * Get request query (with GET parameters)
         * @return string
        **/
        public static function query() {
            return self::$query;     
        }
        
        /**
         * Get request language 
         * @return string
        **/
        public static function language() {
            return self::$language;     
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