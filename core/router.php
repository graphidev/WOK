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

    class Router {

        protected static $routes    = array();
        protected static $patterns  = array();
        protected static $filters   = array();


        /**
         * Get an URL from a route
         * URI data can be specified with the second parameter
         * @param   string      $route      The route name (this usualy is the action name (with the controller))
         * @param   array       $data       The required data to complete the route URI
         * @exemple Router::url('controller:action', array('param_name'=>'value', ...));
        **/
        public static function url($action, $parameters = array()) {

			if(!isset(self::$routes[$action]))
				trigger_error('Router::url() : undefined action '.$action, E_USER_ERROR);

			$route = self::$routes[$action];
			$domain = (!empty($route['domain']) ? $route['domain'] : SYSTEM_DOMAIN);

			// Check parameters values
			foreach($route['parameters'] as $name => $regexp) {
				if(!isset($parameters[$name]))
					trigger_error('Router::url() : missing parameter '. $name, E_USER_ERROR);

				elseif(!preg_match('#^'.$regexp.'$#isU', $parameters[$name]))
					trigger_error('Router::url() : parameter "'. $name .'" doesn\'t match the REGEXP', E_USER_ERROR);
			}

			foreach($parameters as $index => $value) {
				$route['uri'] = str_replace(":$index", $value, $route['uri']);
			}

			return path($route['uri'], $domain);
        }

        /**
         * Define a route
		 * @param   string              $uri            The request URI
		 * @param   string|Closure      $action         The route action (can be a controller method or a callback)
		 * @param   array      			$additionals    Optional parameters : domain(string), method(string|array), parameters(array), filter(string|Closure)
         * @param   array               $filters         The route filter's name or callback
        **/
        public static function register($name, $route = array()) {

			self::$routes[$name] = array_merge(array(
                'uri' => null,
                'action' => null,
				'parameters' => array(),
				'domain' => null,
				'method' => null,
				'filters' => array()
            ), $route);

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

		/**
		 * Check route matching by action
		 * @param	string		$controller		The controller action (controller:action)
		 * @param	array		$parameters		Associated parameters
		 * @param 	boolean		$filtering		Check filters
		**/
		public static function match($controller, $parameters) {

			if(!isset(self::$routes[$controller]))
				return false;

			/**
			 * @TODO : CHECK PARAMETERS VALUES (REGEXP)
			**/

			$route = self::$routes[$controller];
			foreach($route['parameters'] as $name => $regexp) {
				if(!isset($parameters[$name]))
					return false;

				elseif(!preg_match('#^'.$regexp.'$#isU', $parameters[$name]))
					return false;
			}

			// Set route's data
			$data = new StdClass;
			$data->controller = $controller;
			$data->parameters = $parameters;

			// Check route filters
			if(!empty($route['filters'])) {

				foreach($route['filters'] as $filter) {

					if(is_string($filter)) {
						if(!isset(self::$filters[$filter]))
								trigger_error('Router: Undefined filter '.$name, E_USER_ERROR);

						$filter = self::$filters[$filter];
					}

					$filtering = call_user_func($filter, array($data));

					if(is_null($filtering) || (is_bool($filtering) && $filtering))
						continue;

					elseif(!$filtering)
						return false;

					elseif($filtering instanceof Response)
						return $filtering;

				}

			}

			return $data;

		}

        /**
         * Browse routes until it find a matching one.
         * Return false for not found route
		 * @param	string		$uri		The HTTP request URI
		 * @param 	string		$method		The HTTP request method
		 * @param 	string		$domain		The HTTP request domain
        **/
        public static function find($uri, $method, $domain) {

            foreach(self::$routes as $route) {

				// Prepare route regexp of the URI
                $regexp = $route['uri'];
                $patterns = array_merge(self::$patterns, $route['parameters']);

                foreach($patterns as $name => $pattern) {
                    $regexp = str_replace(":$name", "(?<$name>$pattern)", $regexp);
                }

                // Check the route
                if((isset($route['uri']) && ($route['uri'] == $uri || preg_match('#^'.$regexp.'$#isU', $uri, $parameters)))   // Check URI
                   && (empty($route['method']) || in_array($method, $route['method']))                        				  // Check method
                   && (empty($route['domain']) || (!empty($route['domain']) && $route['domain'] == $domain))  				  // Check domain
                  ) {

                    // Current parameters
                    if(!isset($parameters) || is_null($parameters)) $parameters = array();
                    $parameters = array_intersect_key($parameters, $patterns);


					return self::match($controller, $parameters); // Check parameters matching

                }

            }

            return false; // No route

        }

    }

?>
