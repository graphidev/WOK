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

                if(($request['url'] == self::$URI || preg_match('#^'.$request['url'].'$#isU', self::$URI)) 
                   && in_array(self::$method, $request['methods'])
                   && ($request['domain'] == self::$domain || (self::$domain != SYSTEM_DOMAIN && in_array($request['domain'], explode(' ', SYSTEM_DOMAIN_ALIAS))))):
                    
                    $break = (count($request['parameters']) ? false : true);
                    $index = 1; // URI parameter index
                        
                    foreach($request['parameters'] as $i => $param) {
                        switch($param['type']) {
                            case 'URI':
                            
                                $value = preg_replace('#^'.$request['url'].'$#isU', '$'.$index, self::get('URI'));
                                if(preg_match('#^'.$param['regexp'].'$#isU', $value)):
                                    self::$parameters['URI'][$param['name']] = $value;
                                    $break = true;
                                else:
                                    $break = false;
                                endif;
                                
                                $index++;
                                
                                break;
                                
                            case 'POST':
                                    
                                if(isset(self::$parameters['POST'][$param['name']]) 
                                   && preg_match('#^'.$param['regexp'].'$#isU', self::$parameters['POST'][$param['name']]))
                                    $break = true;
                                else
                                    $break = false;
                                
                                break;
                                
                            case 'GET':
                                
                                if(isset(self::$parameters['GET'][$param['name']])
                                   && preg_match('#^'.$param['regexp'].'$#isU', self::$parameters['GET'][$param['name']]))
                                    $break = true;
                                else
                                    $break = false;
                                
                                break;
                                    
                            case 'FILE':
                                
                                if(isset(self::$parameters['FILES'][$param['name']]))
                                    $break = true;
                                else
                                    $break = false;
                                
                                break;
                        }
                            
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
                $method = self::get('method');
            
            return (!empty(self::$parameters[$method][$name]) ? self::$parameters[$method][$name] : false);
        }
        
        /**
         * Get request information
        **/
        public static function get($name) {
            return (!empty(self::$$name) ? self::$$name : false);   
        }

    }

?>