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
        
        protected static $routes    = array();
        protected static $patterns  = array();
        protected static $filters   = array();
        
        /**
         * Prevent the usage of the __construct() method
        **/
        protected function __construct() {
        
            $manifest = SYSTEM_ROOT.PATH_VAR.'/manifest.xml';
            $tmp = SYSTEM_ROOT.PATH_TMP.'/manifest.json';
            
            if(!SYSTEM_DEBUG && file_exists($tmp) 
               && filemtime($manifest) < filemtime($tmp)) {
                
                $manifest = json_decode(file_get_contents($tmp), true);
                self::$routes = isset($manifest['routes']) ? $manifest['routes'] : array();
                self::$patterns = isset($manifest['patterns']) ? $manifest['patterns'] : array();
                
            }
            else {
                
                $dom = new DOMDocument();
                $dom->load($manifest);
                $manifest = $dom->getElementsByTagName('manifest')->item(0);
                // Parse global patterns
                foreach($manifest->getElementsByTagName('pattern') as $pattern) {
                    self::$patterns[$pattern->getAttribute('name')] = $pattern->getattribute('regexp');   
                }    
                
                // Parse standalone requests
                foreach($manifest->getElementsByTagName('route') as $route) {
                    
                    $parameters = array();
                    foreach($route->getElementsByTagName('param') as $param) {
                        $parameters[$param->getAttribute('name')] = $param->getAttribute('regexp');    
                    }
                    
                    self::$routes[] = array(
                        'uri' => ($route->hasAttribute('uri') ? $route->getAttribute('uri') : ''),
                        'method' => ($route->hasAttribute('method') ? explode('|', $route->getAttribute('method')) : array()),
                        'languages' => ($route->hasAttribute('languages') ? explode('|', $route->getAttribute('languages')) : array()),
                        'action' => $route->getAttribute('action'),
                        'parameters' => $parameters,
                        'domain' => ($route->hasAttribute('domain') ? str_replace('~', SYSTEM_DOMAIN, $route->getAttribute('domain')) : null),
                        'filter' => ($route->hasAttribute('filter') ? $route->getAttribute('filter') : null),
                    );
                
                }

                if(!SYSTEM_DEBUG) { // Register cached manifest
					mkpath(root(PATH_TMP));
                    $json = fopen($tmp, 'w+');
                    fwrite($json, json_encode(array(
                        'routes' => self::$routes,
                        'patterns' => self::$patterns
                    )));
                    fclose($json);
                }
            }
            
        }
                                   
        
        /**
         * Get an URL from a route
         * URI data can be specified with the second parameter
         * @param   string      $route      The route name (this usualy is the action name (with the controller))
         * @param   array       $data       The required data to complete the route URI
         * @exemple Manifest::url('controller:action', array('param_name'=>'value', ...));
        **/
        public static function url($action, $parameters = array()) {
            foreach(self::$routes as $key => $route) {
                
                if($route['action'] == $action):
                    $domain = (!empty($route['domain']) ? $route['domain'] : SYSTEM_DOMAIN);
                    
                    foreach($parameters as $index => $value) {
                        $route['uri'] = str_replace(":$index", $value, $route['uri']);
                    }
                
                    return path($route['uri'], $domain);
                    break;
                endif;
            }
            
            return false;
        }
        
        /**
         * Define a route
		 * @param   string              $uri            The request URI
		 * @param   string|Closure      $action         The route action (can be a controller method or a callback)
		 * @param   array      			$additionals    Optional parameters : domain(string), method(string|array), parameters(array), filter(string|Closure)
         * @param   string|Closure      $filter         The route filter's name or callback
        **/
        public static function register($uri, $action, array $additionals = array(), $filter = null) {
           	
			if(!empty($additionals['method']) && is_string($additionals['method']))
				$additionals['method'] = explode('|', $additionals['method']);

			self::$routes[] = array_merge(array(
                'uri' => $uri,
                'action' => $action,
				'filter' => $filter
            ), $additionals);
            
        }
        
        /**
         * Define a pattern regexp
         * @param   string      $name       Pattern's name
         * @param   Closure     $regexp     Pattern's regexp
        **/
        public static function pattern($name, $regexp) {
            self::$patterns[$name] = $regexp;   
        }
        
        /**
         * Define routes filter
         * @param   string      $name       Filter's name
         * @param   Closure     $callback   Filter's callback
        **/
        public static function filter($name, Closure $callback) {
            self::$filters[$name] = $callback;   
        }
        
    }

?>