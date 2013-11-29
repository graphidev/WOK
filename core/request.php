<?php

    /**
     * Request class
     * Contains the entry point and requests informations
    **/
    
    class Request extends Route {
        
        protected static $query         = false;
        protected static $parameters    = array();
        
        /**
         * Build request
        **/
        public static function init() {
            if(!self::$query):
                $query          = str_replace(SYSTEM_DIRECTORY, '', $_SERVER['REQUEST_URI']);
                $static         = preg_replace('#(/[a-z0-9\.-]+)?(\?(.+))?$#iSU', "$1", $query);
                $additional     = str_replace($static, '', preg_replace('#([a-z0-9/\.-]+)?(\?(.+))$#iSU', "$3", $query));	
                
                /**
                 * Define global request informations
                **/
                self::$query                = new StdClass();
                self::$query->domain        = $_SERVER['HTTP_HOST'];
                self::$query->method        = strtoupper($_SERVER['REQUEST_METHOD']);
                self::$query->port          = $_SERVER['SERVER_PORT'];
                self::$query->URI           = str_replace(path(), '', path($static));
                self::$query->secured       = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
                self::$query->ajax          = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
                self::$query->range         = (isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : false);
                self::$query->format        = pathinfo($static, PATHINFO_EXTENSION);
                    
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
                
                if(!empty($_POST)): // POST parameters
                    self::$parameters['POST'] = &$_POST;
                endif;
                
                if(!empty($_FILES)): // FILES parameters
                    self::$parameters['FILES'] = &$_FILES;
                endif;
                
                /**
                 * Add URI parameters and action
                **/
                foreach(parent::$manifest as $i => $request) {

                    if(($request['url'] == self::$query->URI || preg_match('#^'.$request['url'].'$#isU', self::$query->URI)) 
                       && in_array(self::$query->method, $request['methods'])
                       && ($request['domain'] == self::$query->domain || (self::$query->domain != SYSTEM_DOMAIN && in_array($request['domain'], explode(' ', SYSTEM_DOMAIN_ALIAS))))):
                    
                        $break = (count($request['parameters']) ? false : true);
                        $index = 1; // URI parameter index
                        
                        
                        foreach($request['parameters'] as $name => $param) {
                            switch($param['type']) {
                                case 'URI':
                                    $value = preg_replace('#^'.$request['url'].'$#isU', '$'.$index, self::get('URI'));
                                    if(preg_match('#^'.$param['regexp'].'$#isU', $value))
                                        $break = true;
                                    else
                                        $break = false;
                                
                                    $index++;
                                
                                    break;
                                
                                case 'POST':
                                    
                                    if(isset(self::$parameters['POST'][$name]) 
                                       && preg_match('#^'.$param['regexp'].'$#isU', self::$parameters['POST'][$name]))
                                        $break = true;
                                    else
                                        $break = false;
                                
                                    break;
                                
                                case 'GET':
                                
                                    if(isset(self::$parameters['GET'][$name])
                                      && preg_match('#^'.$param['regexp'].'$#isU', self::$parameters['GET'][$name]))
                                        $break = true;
                                    else
                                        $break = false;
                                
                                    break;
                                    
                                case 'FILE':
                                
                                    if(isset(self::$parameters['FILES'][$name]))
                                        $break = true;
                                    else
                                        $break = false;
                                
                                    break;
                            }
                            
                        }
                    
                        if($break):
                            self::$query->action = $request['action'];
                            break;
                        endif;
                    
                    endif;
                }
                        
            endif;                     
            
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
            return (!empty(self::$query->$name) ? self::$query->$name : false);   
        }

    }

?>