<?php

    /**
     * Manifest class
     * Generate manifest data and temporary files
    **/

    class Manifest {
        
        protected static $manifest = array();
        
        /**
         * Get URL from an action
        **/
        public static function url($action, $data = array()) {
            if(!empty(self::$manifest[$action])):
                $url = self::$manifest[$action]['url'];
                foreach($data as $index => $value) {
                    $url = str_replace(":$index", $value, $url);   
                }
                return path($url);
            else:
                return false;
            endif;
        }
        
        /**
         * Build tmp manifest
        **/
        public static function init() {
            $tmp = root(PATH_TMP . '/manifest.son');
            $source = root(PATH_VAR.'/manifest.xml');
            
            if(file_exists($tmp) && (!file_exists($source) || filemtime($source) < filemtime($tmp))):
                self::$manifest = json_decode(file_get_contents($tmp), true);
            
            elseif(file_exists($source)):                
                $dom = new DOMDocument();
                $dom->load($source);
                $manifest = $dom->getElementsByTagName('manifest')->item(0);
                $requests = $manifest->getElementsByTagName('request');
                
                foreach($requests as $case) {
                    $url = $case->getAttribute('url');
                    $action = ($case->hasAttribute('action') ? $case->getAttribute('action') : null);
                    if($case->hasAttribute('domain'))
                        $domain = str_replace('~', str_replace('www.', '', SYSTEM_DOMAIN), $case->getAttribute('domain'));
                    else
                        $domain = SYSTEM_DOMAIN;
                    
                    if($case->hasAttribute('methods') && $case->getAttribute('methods') != '' && strtoupper($case->getAttribute('methods')) != 'ANY')
                        $methods = explode('|', strtoupper($case->getAttribute('methods')));
                    else
                        $methods = array('GET', 'POST', 'HEAD', 'PUT');
                                           
                    foreach($case->getElementsByTagName('param') as $param) {
                        $name = $param->getAttribute('name');
                        $type = $param->hasAttribute('type') ? $param->getAttribute('type') : 'URI';
                        $regexp = $param->hasAttribute('regexp') && $param->getAttribute('regexp') != '' ? $param->getAttribute('regexp') : '.+';
                        
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
                        
                        if($type == 'URI') // Replace URI parameters by parameter REGEXP in $url
                            $url = str_replace(":$name", "($regexp)", $url);
                            
                       $parameters[$name] = array(
                            'type' => strtoupper($type),
                            'regexp' => $regexp,
                        );
                                         
                    }
                    
                    self::$manifest[$action] = array(
                        'url' => $url,
                        'methods' => $methods,
                        'action' => $action,
                        'domain' => $domain,
                        'parameters' => (!empty($parameters) ? $parameters : array())
                    );
                }
                
            /* Remove this comment !!!
                $json = fopen($tmp, 'w+');
                fwrite($json, json_encode(self::$manifest));
                fclose($json);
            */
            endif;
        }
        
    }

?>