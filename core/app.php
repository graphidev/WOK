<?php

    class App {
     
        protected static $manifest = array();
        protected static $settings = array();
        
        public function __construct() {
            self::_manifest();
        }
        
        
        /**
         * Get URL from an action
        **/
        public static function url($action, $data = array()) {
            foreach(self::$manifest as $key => $request) {
                
                if($request['name'] == $action):
                    $uri = $request['uri'];
                    foreach($data as $index => $value) {
                        $uri = str_replace(":$index", $value, $uri);
                    }
                    return path($uri);
                    break;
                endif;
            }
            
            return false;
        }
        
        
        /**
         * Load manifest for App usage
        **/
        private static function _manifest() {
            $tmp = root(PATH_TMP . '/manifest.json');
            $source = root(PATH_VAR.'/manifest.xml');
            
            if(file_exists($tmp) && (!file_exists($source) || filemtime($source) < filemtime($tmp))):
                self::$manifest = json_decode(file_get_contents($tmp), true);
            
            elseif(file_exists($source)):                
                $dom = new DOMDocument();
                $dom->load($source);
                $manifest = $dom->getElementsByTagName('manifest')->item(0);
                $requests = $manifest->getElementsByTagName('request');
                
                // Analyse request
                foreach($requests as $case) {
                    $parameters = array();
                    
                    // define request options
                    $uri = $case->getAttribute('uri');
                    $uri_regexp = $uri;
                    $action = $case->getAttribute('action');
                    $route = ($case->hasAttribute('name') ? $case->getAttribute('name') : $action);
                    if($case->hasAttribute('domain'))
                        $domain = str_replace('~', str_replace('www.', '', SYSTEM_DOMAIN), $case->getAttribute('domain'));
                    else
                        $domain = SYSTEM_DOMAIN;
                    
                    if($case->hasAttribute('languages') && $case->getAttribute('languages') != '' && strtoupper($case->getAttribute('languages')) != 'ANY')
                        $languages = explode(' ', $case->getAttribute('languages'));
                    else
                        $languages = explode(' ', SYSTEM_LANGUAGES);
                    
                    if($case->hasAttribute('methods') && $case->getAttribute('methods') != '' && strtoupper($case->getAttribute('methods')) != 'ANY')
                        $methods = explode(' ', strtoupper($case->getAttribute('methods')));
                    else
                        $methods = array('GET', 'POST', 'HEAD', 'PUT');
                    
                    // Define request parameters (URI, GET, POST, ...)
                    foreach($case->getElementsByTagName('param') as $param) {
                        $name = $param->getAttribute('name');
                        $type = $param->hasAttribute('type') ? $param->getAttribute('type') : 'URI';
                        $regexp = $param->hasAttribute('regexp') && $param->getAttribute('regexp') != '' ? $param->getAttribute('regexp') : '.+';
                        
                        if($type == 'URI'):
                            
                            // Generate regexp from type
                            switch($regexp) {
                                case 'any':
                                    $regexp = '.+';
                                    break;
                                case 'string':
                                    $regexp = '[a-z0-9_-]+';
                                    break;
                                case 'integer':
                                    $regexp = '[0-9]+';
                                    break;
                            }
                        
                            // Replace URI parameters by parameter REGEXP in $url
                            $uri_regexp = str_replace(":$name", "($regexp)", $uri_regexp);
                        endif;                    
                            
                        $parameters[] = array(
                            'name' => $name,
                            'type' => strtoupper($type),
                            'regexp' => $regexp,
                        );
                                         
                    }
                    
                    self::$manifest[] = array(
                        'uri' => $uri,
                        'regexp' => $uri_regexp,
                        'name' => $route,
                        'methods' => $methods,
                        'languages' => $languages,
                        'action' => $action,
                        'domain' => $domain,
                        'parameters' => (!empty($parameters) ? $parameters : array())
                    );
                                        
                }
                
                $json = fopen($tmp, 'w+');
                fwrite($json, json_encode(self::$manifest));
                fclose($json);

            endif;   
        }
        
        
        public static function inc($library) {
            if(file_exists(SYSTEM_ROOT.PATH_LIBRARIES."/$library.php"))
                require_once(SYSTEM_ROOT.PATH_LIBRARIES."/$library.php");
        }
        
        
    }

?>