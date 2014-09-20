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

    class Router extends Manifest {
        
        protected function __construct() {
            parent::__construct();   
        }
        
        public static function instantiate() {
            new self;  
        }
        
        /**
         * Browse routes until it find a matching one. 
         * Return false for not found route
        **/        
        public static function dispatch() {
            foreach(parent::$routes as $route) {
                
                // Prepare route regexp of the URI
                $regexp = $route['uri'];
                $patterns = array_merge(parent::$patterns, $route['parameters']);

                foreach($patterns as $name => $pattern) {
                    $regexp = str_replace(":$name", "(?<$name>$pattern)", $regexp);
                }
                
                                                
                // Check the route
                if((empty($request['domain']) || (!empty($request['domain']) && $request['domain'] == Request::domain()))       // Check domain
                   && (empty($route['method']) || in_array(Request::method(), $route['method']))                                // Check method
                   && ($route['uri'] == Request::uri() || preg_match('#^'.$regexp.'$#isU', Request::uri(), $parameters))        // Check URI
                  ) {                
                    
                    // Current parameters
                    if(!isset($parameters) || is_null($parameters)) $parameters = array();
                    $parameters = array_intersect_key($parameters, $patterns);
                    @list($class, $action) = explode(':', strtolower($route['action']));
                    
                    // Apply filter if it exists
                    if(!empty($route['filter'])) {
                        
                        if(!isset(parent::$filters[$route['filter']]))
                                trigger_error("Undefined filter {$route['filter']} for this route : $class:$action", E_USER_ERROR);
                                                
                        $filtering = call_user_func_array(parent::$filters[$route['filter']], array($route, $parameters));
                                                
                    }
                    else {
                        $filtering = null;
                    }
                    
                    // This is THE route, execute the dispatcher
                    if(is_null($filtering) || $filtering) {
                        
                        if($filtering instanceof Response)
                            $controller = $filtering;   
                        
                        elseif(is_closure($route['action']))
                            $controller = $route['action'];
                            
                        else {

                            if(!method_exists($module = "\\Controllers\\$class", $action)) // Undefined controller or action
                                trigger_error("Undefined controller/action for this route : $class:$action", E_USER_ERROR);
                            
                            $controller = array(new $module, $action);
                        }
                        
                        /* Dispatch response */
                        if($controller instanceof Response)
                            $controller->render();

                        elseif(is_null($response = call_user_func_array($controller, $parameters)))
                            Response::null(-200)->render();

                        elseif($response instanceof Response)
                            $response->render();

                        else trigger_error('Controller returned value must be a Response object', E_USER_ERROR);
                        
                        return true; // Shutdown loop and function
                    }
                    
                }
                
            }
            
            // Bad request, no route : send a 404 response
            return false;
            
        }

    }

?>