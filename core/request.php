<?php

    /**
     * Request class
     * Contains the entry point and requests informations
    **/
    
    class Request extends Route {
        
        protected static $query         = false;
        protected static $parameters    = array();
        protected static $language      = SYSTEM_DEFAULT_LANGUAGE;
        
        /**
         * Build request
        **/
        public static function init() {
            if(!self::$query):
                $query          = str_replace(SYSTEM_DIRECTORY_PATH, '', $_SERVER['REQUEST_URI']);
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
                 * Define session and cookie informations
                **/
                $lifetime = (ini_get('session.cookie_lifetime') ? ini_get('session.cookie_lifetime') : ini_get('session.maxlifetime'));
                session_set_cookie_params($lifetime, '/', null, self::$query->secured, true);
                session_start(); // Start sessions
            
                /**
                 * Define request language
                **/
                $accepted_languages = explode(',', SYSTEM_ACCEPT_LANGUAGES);
                if(Session::language() != false):
                    self::$query->language = Session::get('language');
                                
                
                elseif(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])):
                    $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                    foreach($languages as $i => $language) {
                        $code = str_replace('-', '_', $language);
                        if(in_array($code, $accepted_languages)):
                            self::$query->language = $code;
                            break;
                        else:
                            self::$query->language = SYSTEM_DEFAULT_LANGUAGE;
                        endif;
                    }
                    Session::language(self::$language);
                endif;
                
                if(empty(self::$language)):
                    self::$query->language = SYSTEM_DEFAULT_LANGUAGE;
                endif;
                            
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
                 * Add URI parameters
                **/            
                foreach(parent::$manifest as $i => $request) {
                    if(preg_match('#^'.$request['url'].'$#isU', self::$query->URI) 
                       && ($request['domain'] == self::$query->domain || (self::$query->domain != SERVER_DOMAIN && in_array($request['domain'], explode(' ', SERVER_DOMAIN_ALIAS))))):
                        $break = (count($request['parameters']) ? false : true);
                        foreach($request['parameters'] as $name => $param) {
                            if($param['type'] == 'string' && in_array(self::get('method'), array('GET', 'POST'))):
                                self::$parameters['URI'][$name] = preg_replace('#^'.$request['url'].'$#isU', '$'.$param['position'], self::get('URI'));
                                if(!$param['optional']): 
                                    $break = true; 
                                else:
                                    $break = false;
                                endif;
                            else:                                
                                switch($request['method']) {
                                    case 'POST':
                                        if(!isset($_POST[$name]) && !$param['optional']):
                                            $break = false;
                                        else:
                                            self::$parameters[$request['method']][$name] = $_POST[$name];
                                            $break = true;
                                        endif;
                                    
                                        break;
                                    case 'GET':
                                        if(!isset($_GET[$name]) && !$param['optional'])
                                            $break = false;
                                        else
                                            self::$parameters[$request['method']][$name] = $_GET[$name];
                                            $break = true;
                                        break;
                                    case 'FILES':
                                        if(!isset($_FILES[$name]) && !$param['optional'])
                                            $break = false;
                                        else
                                            self::$parameters[$request['method']][$name] = $_FILES[$name];
                                            $break= true;
                                        break;
                                    default:
                                        $break = false;
                                }
                            endif;
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