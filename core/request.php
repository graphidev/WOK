<?php

    /**
     * Request class
     * Contains the entry point and requests informations
    **/
    
    class Request {
        
        public static $method;
        public static $URI;
        public static $domain;
        private static $action;
        private static $params       = array();
        private static $config       = array();
        
        public static $globales     = array();
                
        public function __construct() {
            $query          = str_replace(SYSTEM_DIRECTORY_PATH, '', $_SERVER['REQUEST_URI']);
            $static         = preg_replace('#(/[a-z0-9\.-]+)?(\?(.+))?$#iSU', "$1", $query);
            $additional     = str_replace($static, '', preg_replace('#([a-z0-9/\.-]+)?(\?(.+))$#iSU', "$3", $query));	
            
            self::$domain       = $_SERVER['HTTP_HOST'];
            self::$method       = strtoupper($_SERVER['REQUEST_METHOD']);
            self::$URI          = str_replace(path(), '', path($static));
            
            
            /**
             * Generate requests config
            **/
            if(empty(self::$access)):
                $source = root(PATH_VAR.'/urls.xml');
                $parsed = root(PATH_TMP.'/urls.json');
            
                if(file_exists($parsed)):
                    if(file_exists($source) && filemtime($source) > filemtime($parsed)):
                        self::init();
                    else:
                        self::$config = json_decode(file_get_contents($parsed), true);
                    endif;
                
                elseif(file_exists($source)):
                    self::init();
                endif;
                
            endif;
            
            
            /**
             * Check parameters
            **/
            if(!empty($_GET)): // GET parameters
                self::$params['GET'] = &$_GET;
            elseif(!empty($additional)):
                foreach(explode('&', $additional) as $i => $parameter) {
                    @list($name, $value) = explode('=', $parameter);
                    self::$params['GET'][$name] = urldecode($value);
                }
            endif;
            
            if(!empty($_POST)): // POST parameters
                self::$params['POST'] = &$_POST;
            endif;
            
            if(!empty($_FILES)): // FILES parameters
                self::$params['FILES'] = &$_FILES;
            endif;
                    
            
            /**
             * Add URI parameters
            **/
            if(!empty(self::$config)):
                foreach(self::$config as $i => $request) {
                    if(preg_match('#^'.$request['url'].'$#isU', self::$URI) && (empty($request['domain']) || $request['domain'] == self::$domain)):
                        foreach($request['parameters'] as $name => $param) {
                            if($param['type'] != $request['method']):
                                /** [IDEA]: (require an other switch) return parameter'value according to type (int, float ...) **/
                                self::$params['URI'][$name] = preg_replace('#^'.$request['url'].'$#isU', '$'.$param['position'], self::$URI);
                                    
                            else:
                                switch($method) {
                                    case 'POST':
                                        self::$params[$request['method']][$name] = $_POST[$name];
                                        break;
                                    case 'GET':
                                        self::$params[$request['method']][$name] = $_GET[$name];
                                        break;
                                    case 'FILES':
                                        self::$params[$request['method']][$name] = $_FILES[$name];
                                        break;
                                }
                            endif;
                        }
                        break;
                    endif;
                }
            endif;
        }
        
        private static function init() {
            $dom = new DOMDocument();
            $dom->load(root(PATH_VAR.'/urls.xml'));
            $access = $dom->getElementsByTagName('access')->item(0);
            $urls = $access->getElementsByTagName('request');
            
            foreach($urls as $case) {
                $url = $case->getAttribute('url');
                $domain = ($case->hasAttribute('domain') ? str_replace('~', SERVER_DOMAIN, $case->getAttribute('domain')) : null);
                $method = ($case->hasAttribute('method') ? strtoupper($case->getAttribute('method')) : 'GET');
                $action = ($case->hasAttribute('action') ? $case->getAttribute('action') : null);
                $count = 1;
                    
                foreach($case->getElementsByTagName('param') as $param) {
                    $name = $param->getAttribute('name');
                    $optional = ($param->hasAttribute('required') && in_array($param->getAttribute('required'), array('false', 'no', 0)) ? true : false);
                    if($param->hasAttribute('type')):
                    $type = $param->getAttribute('type');
                        
                    if($param->hasAttribute('regexp')):
                        $regexp = $param->getAttribute('regexp');
                        
                    else:
                        switch($type) {
                            case 'string':
                                $regexp = '[a-z0-9_-]+';
                                break;
                            case 'integer':
                                $regexp = '[0-9]+';
                                break;
                            case 'float':
                                $regexp = '';
                                break;
                            default:
                                $regexp = '.+';
                        }
                    endif;
                        
                    elseif($param->hasAttribute('regexp')):
                        $type = 'string';
                        $regexp = $param->getAttribute('regexp');
                    else:
                        $type = $method;
                        $regexp = null;
                    endif;
                    
                    // Replace URI parameters by parameter REGEXP in $url
                    if($type != $method):
                        if($optional)
                            $url = str_replace(":$name", "($regexp)?", $url);
                        else
                            $url = str_replace(":$name", "($regexp)", $url);
                    endif;
                        
                   $parameters[$name] = array(
                        'position' => $count,
                        'type' => $type,
                        'regexp' => $regexp,
                        'optional' => $optional
                    );
                    
                    $count++;
                    
                    // create a JSON file
                }
                
                self::$config[] = array(
                    'url' => $url,
                    'method' => $method,
                    'parameters' => $parameters
                );    
            }
            
            $json = fopen(root(PATH_TMP.'/urls.json'), 'w+');
            fwrite($json, json_encode(self::$config));
            fclose($json);
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