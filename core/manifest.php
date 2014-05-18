<?php
    
    /**
     * Generate and load the XML manifest. 
     * It also can be used in order to get and URL from an action
     *
     * Please note that all the informations about Manifest 
     * structure can be found in the manifest file itself.
     *
     * @package Core
    **/    

    class Manifest {
        
        protected static $manifest = array();
        
        
        /**
         * Get URL from an action
         * @exemple Manifest::url('controller:action', array('param_name'=>'value', ...));
        **/
        public static function url($action, $data = array()) {
            foreach(self::$manifest as $key => $request) {
                
                if($request['name'] == $action):
                    $uri = $request['uri'];
                    $domain = $request['domain'];
                    foreach($data as $index => $value) {
                        $uri = str_replace(":$index", $value, $uri);
                    }
                    return path($uri, $domain);
                    break;
                endif;
            }
            
            return false;
        }
        
        
        /**
         * Load tmp manifest file
        **/
        public static function load() {
            if(!file_exists($source = root(PATH_VAR.'/manifest.xml')))
                return false;
            
            $tmp = root(PATH_TMP . '/manifest.json');

            if(SYSTEM_DEBUG || !file_exists($tmp) || filemtime($source) > filemtime($tmp))
                self::build();
            else
                self::$manifest = json_decode(file_get_contents($tmp), true);
        }
        
        
        /**
         * Build tmp manifest file
        **/
        private static function build() { 
            
            $dom = new DOMDocument();
            $dom->load(root(PATH_VAR.'/manifest.xml'));
            $manifest = $dom->getElementsByTagName('manifest')->item(0);
            $requests = $manifest->getElementsByTagName('request');

            // Analyse request
            foreach($requests as $case) {
                $parameters = array();
                $tokens     = array();
                $cookies    = array();
                $sessions   = array();

                // define request options
                $uri = $case->getAttribute('uri');
                $uri_regexp = $uri;
                $action = $case->getAttribute('action');
                $route = ($case->hasAttribute('name') ? $case->getAttribute('name') : $action);
                
                if($case->hasAttribute('domain'))
                    $domain = str_replace('~', SYSTEM_DOMAIN, $case->getAttribute('domain'));
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
                
                if($case->hasAttribute('types')  && $case->getAttribute('types') != '' && strtoupper($case->getAttribute('types')) != 'ANY')
                    $types = explode(' ', strtoupper($case->getAttribute('types')));
                else
                    $types = array('HTTP', 'XHR','CLI');

                // Define request parameters (URI, GET, POST, ...)
                foreach($case->getElementsByTagName('param') as $param) {
                    $value = array(
                        'name' => $param->getAttribute('name'),
                        'type' => $param->hasAttribute('type') ? strtoupper($param->getAttribute('type')) : 'URI',
                        'regexp' => $param->hasAttribute('regexp') && $param->getAttribute('regexp') != '' ? $param->getAttribute('regexp') : '.+',
                        'value' => $param->nodeValue
                    );

                    if($value['type'] == 'URI') // Replace URI parameters by parameter REGEXP in $url
                        $uri_regexp = str_replace(':'.$value['name'], "(".$value['regexp'].")", $uri_regexp);
                   
                    $parameters[] = $value;
                }
                
                // Define request tokens parameters
                foreach($case->getElementsByTagName('token') as $token) {
                    $tokens[] = array(
                        'name' => $token->getAttribute('name'),
                        'mode' => strtoupper($token->getAttribute('mode')),
                        'time' => $token->hasAttribute('time') ? intval($token->getAttribute('time')) : Token::LIFETIME
                    );
                }
                
                // Define request cookies parameters
                foreach($case->getElementsByTagName('cookie') as $cookie) {
                    if($crypted = $cookie->hasAttribute('crypted'))
                        $crypted = filter_var($cookie->getAttribute('crypted'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    
                    $cookies[] = array(
                        'name' => $cookie->getAttribute('name'),
                        'value' => $cookie->nodeValue,
                        'regexp' => $cookie->hasAttribute('regexp') ? $cookie->getAttribute('regexp') : null,
                        'crypted' =>  $crypted,
                    );
                }
                
                
                // Define request sessions parameters
                foreach($case->getElementsByTagName('session') as $session) {
                    
                    $sessions[] = array(
                        'name' => $session->getAttribute('name'),
                        'value' => $session->nodeValue,
                        'regexp' => $session->hasAttribute('regexp') ? $session->getAttribute('regexp') : null
                    );
                }
                
                
                // Define request settings
                self::$manifest[] = array(
                    'uri' => $uri,
                    'regexp' => $uri_regexp,
                    'name' => $route,
                    'methods' => $methods,
                    'languages' => $languages,
                    'action' => $action,
                    'domain' => $domain,
                    'types' => $types,
                    'parameters' => $parameters,
                    'tokens' => $tokens,
                    'cookies' => $cookies,
                    'sessions' => $sessions,
                );

            }

            $json = fopen(root(PATH_TMP . '/manifest.json'), 'w+');
            fwrite($json, json_encode(self::$manifest));
            fclose($json);
            
        }
        
    }

?>