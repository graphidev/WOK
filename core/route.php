<?php

    /**
     * Manifest class
     * Generate manifest data and temporary files
    **/

    class Route {
        
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
            if(file_exists($tmp)):
                self::$manifest = json_decode(file_get_contents($tmp), true);
            else:                
            
                $dom = new DOMDocument();
                $dom->load(root(PATH_VAR.'/manifest.xml'));
                $manifest = $dom->getElementsByTagName('manifest')->item(0);
                $requests = $manifest->getElementsByTagName('request');
                
                foreach($requests as $case) {
                    $url = $case->getAttribute('url');
                    $domain = ($case->hasAttribute('domain') ? str_replace('~', str_replace('www.', '', SYSTEM_DOMAIN), $case->getAttribute('domain')) : SYSTEM_DOMAIN);
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
                    }
                    
                    self::$manifest[$action] = array(
                        'url' => $url,
                        'method' => $method,
                        'action' => $action,
                        'domain' => $domain,
                        'parameters' => (!empty($parameters) ? $parameters : array())
                    );
                }
                
                $json = fopen(root(PATH_TMP.'/manifest.json'), 'w+');
                fwrite($json, json_encode(self::$manifest));
                fclose($json);
            
            endif;
        }
        
    }

?>