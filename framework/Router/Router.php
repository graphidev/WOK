<?php

    /**
    * Web Operational Kit
    * The neither huger nor micro humble framework
    *
    * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @license     BSD <license.txt>
    **/

    namespace Router;

    /**
     * The Router class provide a way to store
     * and fetch a routes collection.
    **/
    class Router {

        /**
         * @var   string    Controllers namespace prefix
        **/
        protected $namespace   =    'Controllers';

        /**
         * @var   array     Routes collection
        **/
        protected $routes      = array();


        /**
         * Instanciate router
         * @param string    $prefix        Controllers namespace prefix
        **/
        public function __construct($namespace = 'Controllers') {
            $this->namespace = $namespace;
        }


        /**
         * Register a new route
         * @param   string      $action     Associated route action
         * @param   Route       $route      Route instance
        **/
        public function addRoute($action, Route $route) {
            $this->routes[$action] = $route;
        }


        /**
         * Check if a route have been defined
         * @param   string        $action     Associated route action
         * @return  true|false
        **/
        public function hasRoute($action) {
            return isset($this->routes[$action]);
        }


        /**
         * Get a route by it's target action
         * @param   string        $action     Associated route action
         * @return  Route|false
        **/
        public function getRoute($action) {
            if(!$this->hasRoute($action))
                return false;

            return $this->routes[$action];
        }


        /**
         * Try to retrieve and generate a route action
         * @param   string      $method         Current request method
         * @param   string      $domain         Current request domain
         * @param   string      $uri            Current request URI
         * @return  Closure
        **/
        public function fetch($method, $domain, $uri) {

            foreach($this->routes as $target => $route) {

                // Invalid domain
                if(!empty($route->domain) && $route->domain != $domain)
                    continue;

                // Invalid method
                if(!empty($route->methods) && (!in_array($method, $route->methods)))
                    continue;

                // Prepare parameters
                $pattern = $route->pattern;
                foreach($route->parameters as $name => $regexp) {
                    $pattern = str_replace('{'.$name.'}', "(?<$name>$regexp)", $pattern);
                }

                // Invalid pattern
                if($route->pattern != $uri && !preg_match('#^'.$pattern.'$#isU', $uri, $parameters))
                    continue;

                $method     = substr(strstr($target, '->', false), 2);
                $controller = strstr($target, '->', true);
                $controller = $this->namespace.'\\'.$controller;

                if(empty($parameters))
                    $parameters = array();

                return new Dispatcher($controller, $method, $parameters);

            }

            // Route not Found
            return false;

        }

    }
