<?php

    /**
     * Define Manifest action to use and 
     * contains request informations 
     * (parameters, headers, URI, domain, ...)
     *
     * @package Core
    **/
    class Request extends Manifest {
        
        protected static $uri       = '';
        protected static $method    = '';
        protected static $format    = '';
        protected static $action    = '';
        protected static $parameters    = array(
            'URI' => array(),
            'GET' => array(),
            'POST' => array(),
            'FILES' => array()
        );
        
        /**
         * Build request and define current controller:action to use
        **/
        public static function init() {
            $query          = str_replace(SYSTEM_DIRECTORY, '', $_SERVER['REQUEST_URI']);
            $static         = preg_replace('#/([a-z0-9\.-/]+)?(\?(.+))?$#iSU', "$1", $query);
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
                
            if(!empty($_POST)) // POST parameters
                self::$parameters['POST'] = strip_magic_quotes($_POST);
                
            if(!empty($_FILES)) // FILES parameters
                self::$parameters['FILES'] = &$_FILES;
            
            /**
             * Add URI parameters and action
            **/
            foreach(parent::$manifest as $request) {
                
                if(($request['regexp'] == self::$uri || preg_match('#^'.$request['regexp'].'$#isU', self::$uri))
                   && in_array(self::$method, $request['methods'])
                   && $request['domain'] == self::domain()
                  && (!Session::has('language') || in_array(Session::get('language'), $request['languages']))):

                    $break = (count($request['parameters']) ? false : true);
                    $index = 1; // URI parameter index
                    
                    foreach($request['parameters'] as $param) {
                        
                        // URI parameters
                        if($param['type'] == 'URI'):
                        
                            $value = preg_replace('#^'.$request['regexp'].'$#isU', '$'.$index, self::get('uri'));
                            if(preg_match('#^'.$param['regexp'].'$#isU', $value)):
                                self::$parameters['URI'][$param['name']] = $value;
                                $break = !$break ? false : true;
                            else:
                                $break = false;
                            endif;
                        
                            $index++;
                        
                        // FILES parameters
                        elseif($param['type'] == 'FILE' && isset(self::$parameters['FILES'][$param['name']])):
                            $break = !$break ? false : true;
                        
                        // Globals (GET, POST, ...) parameters
                        elseif(isset(self::$parameters[$param['type']][$param['name']])):
                        
                            $value = &self::$parameters[$param['type']][$param['name']];
                                                    
                            if((!empty($param['value']) && $param['value'] == $value) 
                               || empty($param['regexp']) 
                               || $param['regexp'] == 'any' 
                               || $param['type'] == 'FILE' 
                               || $param['regexp'] == gettype($value) 
                               || ($param['regexp'] == 'numeric' && is_numeric($value)) 
                               || preg_match('#^'.$param['regexp'].'$#isU', $value)):
                                $break = !$break ? false : true;
                                                        
                            else:
                                $break = false;
                        
                            endif;
                        
                        else:
                            
                            $break = false;
                        
                        endif;
                        
                    }
                    
                    
                    // Check tokens parameters
                    foreach($request['tokens'] as $token) {
                        if(!empty(self::$parameters[$token['mode']][$token['name']]))
                            $break = (!$break) ? false : Token::authorized($token['name'], self::$parameters[$token['mode']][$token['name']], $token['time']);
                        else
                            $break = false;
                    }
                    
                
                    // Check cookies parameters
                    foreach($request['cookies'] as $cookie) {
                        if($exists = Cookie::exists($cookie['name']))
                            $value = Cookie::get($cookie['name'], $cookie['crypted']);
                        
                        if($exists && 
                            (
                                (!empty($cookie['value']) && $cookie['value'] == $value) 
                                || $cookie['regexp'] == gettype($value) 
                                || ($cookie['regexp'] == 'numeric' && is_numeric($value))  
                                || preg_match('#'.$cookie['regexp'].'#', $value)
                                || (empty($cookie['value']) && empty($cookie['regexp'])))
                            )
                            $break = (!$break) ? false : true;
                            
                         else
                             $break = false;
                    }
                    
                    // Check session parameters
                    foreach($request['sessions'] as $session) {                        
                        if(Session::has($session['name']) && 
                           
                            (
                                (!empty($session['value']) && $session['value'] == Session::get($session['name'])) 
                                || $session['regexp'] == gettype($value) 
                                || ($session['regexp'] == 'numeric' && is_numeric(Session::get($session['name'])))  
                                || preg_match('#'.$session['regexp'].'#', Session::get($session['name'])))
                                || (empty($session['value']) && empty($session['regexp']))
                            )
                            $break = (!$break) ? false : true;
                            
                         else
                             $break = false;
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
         * Get FILES parameter
         * @param string    $name
         * @return string
        **/
        public static function file($name) {
            return self::parameter($name, 'FILES');  
        }
        
        /**
         * Get FILES parameter
         * @param string    $name
         * @return string
        **/
        public static function segment($name) {
            return self::parameter($name, 'URI');  
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