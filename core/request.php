<?php

    /**
     * Request class
     * Contains the entry point and requests informations
    **/
    
    class Request extends App {
        
        protected static $URI         = false;
        protected static $domain        = null;
        protected static $method        = null;
        protected static $port          = null;
        protected static $secured       = null;
        protected static $ajax          = null;
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
            $static         = preg_replace('#(/[a-z0-9\.-]+)?(\?(.+))?$#iSU', "$1", $query);
            $additional     = str_replace($static, '', preg_replace('#([a-z0-9/\.-]+)?(\?(.+))$#iSU', "$3", $query));	
                
            /**
             * Define global request informations
            **/
            self::$domain        = $_SERVER['HTTP_HOST'];
            self::$method        = strtoupper($_SERVER['REQUEST_METHOD']);
            self::$port          = $_SERVER['SERVER_PORT'];
            self::$URI           = str_replace(path(), '', path($static));
            self::$secured       = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            self::$ajax          = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
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
                
                if(($request['regexp'] == self::$URI || preg_match('#^'.$request['regexp'].'$#isU', self::$URI))
                   && in_array(self::$method, $request['methods'])
                   && ($request['domain'] == self::$domain 
                       || (self::$domain != SYSTEM_DOMAIN && in_array($request['domain'], explode(' ', SYSTEM_DOMAIN_ALIAS))))
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
                        
                        // FILES parameters
                        elseif($param['type'] == 'FILE' && isset(self::$parameters['FILES'][$param['name']])):
                            $break = true;
                        
                        // Globals (GET, POST, ...) parameters
                        elseif(isset(self::$parameters[$param['type']][$param['name']])):
                        
                            $value =&self::$parameters[$param['type']][$param['name']];

                            if($param['regexp'] == 'any' 
                               || $param['type'] == 'FILE' 
                               || ($param['regexp'] == 'array' && is_array($value))
                               || ($param['regexp'] == 'string' && is_string($value)) 
                               || ($param['regexp'] == ('integer'||'number'||'float') && is_numeric($value))
                               || preg_match('#^'.$param['regexp'].'$#isU', $value))
                                $break = true;        
                        
                            else
                                $break = false;
                        
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
        **/ 
        public static function parameter($name, $method = null) {
            if(empty($method))
                $method = self::$method;
            
            return (!empty(self::$parameters[$method][$name]) ? self::$parameters[$method][$name] : false);
        }
        
        /**
         * Check request informations
         * Return it if available
        **/
        public static function get($parameter) {
            return (!empty(self::$$parameter) ? self::$$parameter : false);  
        }
        
        /**
         * Check secured connexion
        **/
        public static function secured() {
            return self::$secured;     
        }
        
        
        /**
         * Check secured connexion
        **/
        public static function ajax() {
            return self::$ajax;     
        }
        
        
        /**
         * Check secured connexion
        **/
        public static function method() {
            return self::$method;     
        }
        
        /**
         * Check secured connexion
        **/
        public static function URI() {
            return self::$URI;     
        }

    }

?>