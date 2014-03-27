<?php

    /**
     * Request class
     * Contains the entry point and requests informations
    **/
    
    class Request extends App {
        
        protected static $uri           = null;
        protected static $domain        = null;
        protected static $method        = null;
        protected static $port          = null;
        protected static $range         = null;
        protected static $format        = null;
        protected static $action        = null;
        protected static $parameters    = array(
            'URI' => array(),
            'GET' => array(),
            'POST' => array(),
            'FILES' => array()
        );
        
        /**
         * Build request
        **/
        public function __construct() {
            $query          = str_replace(SYSTEM_DIRECTORY, '', $_SERVER['REQUEST_URI']);
            $static         = preg_replace('#/([a-z0-9\.-]+)?(\?(.+))?$#iSU', "$1", $query);
            $additional     = str_replace($static, '', preg_replace('#([a-z0-9/\.-]+)?(\?(.+))$#iSU', "$3", $query));	
                
            /**
             * Define global request informations
            **/
            self::$domain        = $_SERVER['HTTP_HOST'];
            self::$method        = mb_strtoupper($_SERVER['REQUEST_METHOD']);
            self::$uri           = $static;
            self::$port          = $_SERVER['SERVER_PORT'];
            self::$range         = (isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : false);
            self::$format        = pathinfo($static, PATHINFO_EXTENSION);
                    
            /**
             * Define request parameters
            **/
            if(!empty($_GET)): // GET parameters
                self::$parameters['GET'] = &$_GET;
            
            elseif(!empty($additional)):
                foreach(explode('&', $additional) as $i => $parameter) {
                    @list($name, $value) = explode('=', $parameter);
                    self::$parameters['GET'][$name] = urldecode($value);
                }
            
            endif;
                
            if(!empty($_POST)) // POST parameters
                self::$parameters['POST'] = &$_POST;
                
            if(!empty($_FILES)) // FILES parameters
                self::$parameters['FILES'] = &$_FILES;
            
            /**
             * Add URI parameters and action
            **/
            foreach(parent::$manifest as $i => $request) {
                
                if(($request['regexp'] == self::$uri || preg_match('#^'.$request['regexp'].'$#isU', self::$uri))
                   && in_array(self::$method, $request['methods'])
                   && $request['domain'] == self::$domain
                  && in_array(Session::language(), $request['languages'])):
                    
                    $break = (count($request['parameters']) ? false : true);
                    $index = 1; // URI parameter index
                        
                    foreach($request['parameters'] as $i => $param) {
                        
                        // URI parameters
                        if($param['type'] == 'URI'):
                        
                            $value = preg_replace('#^'.$request['regexp'].'$#isU', '$'.$index, self::get('URI'));
                            if(preg_match('#^'.$param['regexp'].'$#isU', $value)):
                                self::$parameters['URI'][$param['name']] = $value;
                                $break = true;
                            else:
                                $break = false;
                            endif;
                        
                            $index++;
                        
                        // FILES parameters
                        elseif($param['type'] == 'FILE' && isset(self::$parameters['FILES'][$param['name']])):
                            $break = true;
                        
                        // Globals (GET, POST, ...) parameters
                        elseif(isset(self::$parameters[$param['type']][$param['name']])):
                        
                            $value = &self::$parameters[$param['type']][$param['name']];
        
                            if($param['regexp'] == 'any' 
                               || $param['type'] == 'FILE' 
                               || ($param['regexp'] == 'array' && is_array($value))
                               || ($param['regexp'] == 'string' && is_string($value)) 
                               || ($param['regexp'] == ('integer'||'number'||'float') && is_numeric($value)) 
                               || preg_match('#^'.$param['regexp'].'$#isU', $value)):
                                $break = true; 
                                                        
                            else:
                                $break = false;
                        
                            endif;
                        
                        endif;
                        
                    }
                    
                    if($break):
                        self::$action = $request['action'];
                        break;
                    endif;
                    
                endif;
            }
                        
        }     
        
        
        /**
         * Get request parameter
         * @param string    $name
         * @param string    $method
         * @return mixed
        **/ 
        public static function parameter($name, $method = null) {
            if(empty($method))
                $method = self::$method;
            
            return (!empty(self::$parameters[$method][$name]) ? array_value($name, self::$parameters[$method]) : false);
        }
        
        /**
         * Check request informations
         * @return mixed
        **/
        public static function get($information) {
            return (isset(self::$$information) ? self::$$information : false);  
        }
        
        /**
         * Get header's parameter value
         * @return string
        **/
        public static function header($parameter, $default = false) {
            return (isset($_SERVER[$parameter]) ? $parameter : $default);     
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

    }

?>