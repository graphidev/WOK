<?php

    namespace Framework\Core;

    use \Framework\Utils\Collection;

    class Router {

        /**
         *
        **/

        /**
         * @var array   Routes collection
        **/
        private $routes = array();

        /**
         * @var array   Filters collection
        **/
        private $filters = array();


        public function __construct(/*$services*/) {}


        /**
         * Get an URL from a route
         * URI data can be specified with the second parameter
         * @param   string      $route      The route name (this usualy is the action name (with the controller))
         * @param   array       $data       The required data to complete the route URI
         * @exemple Router::url('controller:action', array('param_name'=>'value', ...));
        **/
        public function url($action, $parameters = array()) {

			if(!isset($this->routes[$action]))
				trigger_error('Router::url() : undefined action '.$action, E_USER_ERROR);

			$route = $this->routes[$action];
			$domain = (!empty($route['domain']) ? $route['domain'] : SYSTEM_DOMAIN);

			// Check parameters values
			foreach($route['parameters'] as $name => $regexp) {
				if(!isset($parameters[$name]))
					trigger_error('Router::url() : missing parameter '. $name .'" for '.$action, E_USER_ERROR);

				elseif(!preg_match('#^'.$regexp.'$#isU', $parameters[$name]))
					trigger_error('Router::url() : parameter "'. $name .'" doesn\'t match the REGEXP for '.$action, E_USER_ERROR);
			}

			foreach($parameters as $index => $value) {
				$route['path'] = str_replace(":$index", $value, $route['path']);
			}

			return path($route['path'], $domain);
        }


        /**
         * Register a route
         * @param string    $action         Route name (controller:action)
         * @param array     $options        Route options (path, parameters, domain, method, filters)
        **/
        public function register($action, array $options = array()) {

            $this->routes[$action] = array_merge(array(
                'path'          => null,
                'parameters'    => array(),
                'domain'        => null,
                'method'        => null, // get, post, put, patch, delete, head, options
                'filters'       => array()
            ), $options);

        }

        public function filter($name, $callback) {
            $this->filters[$name] = $callback;
        }

        /**
         * Check weither a route parameters matches or not
         * @param string    $controller         Controller's name
         * @param string    $action             Controller's action name
         * @param string    $parameters         Controller's action parameters
        **/
        public function match($controller, $action, array $parameters = array()) {

            if(!isset($this->route[$controller.':'.$action]))
                return false;

            $regexps = $this->route[$controller.':'.$action]['parameters'];
            foreach($regexps as $name => $regexp) {
                if(!isset($parameters[$name]) || !preg_match('#^'.$regexp.'$#isU', $parameters[$name]))
                    return false;
            }

            return (object) array(
                'controller'    => $controller,
                'action'        => $action,
                'parameters'    => $parameters
            );
        }


        /**
         * Found the route associated to filters
         * @param   array   $requierements        Route filters
        **/
        public function find(array $requierements) {

            foreach($this->routes as $action => $route) {

                // Set request URI regexp
                if(!empty($route['path'])) {
                    $regexp = $route['path'];
                    foreach($route['parameters'] as $name => $pattern) {
                        $regexp = str_replace(":$name", "(?<$name>$pattern)", $regexp);
                    }
                }


                if(
                    // Check request URI (path)
                    (!empty($requierements['path']) && !empty($route['path']) && ($route['path'] == $requierements['path']
                        || preg_match('#^'.$regexp.'$#isU', $requierements['path'], $parameters)))

                    // Check method
                    && (empty($route['method'])
                        || (!empty($requierements['method']) && (!empty($route['method'])) && in_array($requierements['method'], $route['method'])))

                    // Check domain
                    && (empty($route['domain'])
                        || ((!empty($requierements['domain']) && $route['domain'] == $requierements['domain'])))

                  ) {

                    // Set usable parameters value
                    if(!isset($parameters) || is_null($parameters))
                        $parameters = array();

                    $parameters = array_intersect_key($parameters, $route['parameters']);

                    // Split the controller and its action
                    $controller = strstr($action, ':', true);
                    $method     = substr(strstr($action, ':', false), 1);

                    // Check route filters
                    if(!empty($route['filters'])) {

                        foreach($route['filters'] as $filter) {

                            if(is_string($filter)) {
                                if(!isset($this->$filters[$filter]))
                                    trigger_error('Router: Undefined filter '.$filter, E_USER_ERROR);

                                $filter = $this->$filters[$filter];
                            }

                            $filtering = call_user_func($filter, (object) array(
                                'action'          => $action,
                                'parameters'      => new Collection($parameters)
                            ), $this->services);

                            if(is_string($filtering)) {
                                return $this->find(array(
                                    'action'        =>  $action,
                                    'parameters'    => $parameters
                                ));

                            } elseif($filtering) {
                                continue;
                            }
                            else {
                                break;
                            }

                        }

                    }

                    // Return matched route
                    return (object) array(
                        'controller'    => $controller,
                        'action'        => $method,
                        'parameters'    => $parameters
                    );

                }

            }

            return false;

        }

    }
