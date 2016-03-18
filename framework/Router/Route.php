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
     * The Route class provide a way to store
     * and get a route informations object.
    **/
    class Route {

        protected $action;

        protected $pattern;

        protected $parameters;

        protected $methods;

        protected $domain;


        /**
         * Instanciante route
         * @param   string      $pattern            Route URI pattern
         * @param   array       $parameters         Required route parameters patterns (as [$parameter=>$regexp])
         * @param   array       $methods            Route accepted methods
         * @param   string      $domain             Route accepted domain
        **/
        public function __construct($pattern, array $parameters = array(), array $methods = array(), $domain = null) {

            $this->pattern      = $pattern;
            $this->parameters   = $parameters;
            $this->domain       = str_replace('*', '(.+)', $domain);

            // Methods specific treatments
            $methods = array_map(function($method) {
                return mb_strtoupper($method);
            }, $methods);

             // Replace `HTTP` by HTTP methods list
            if(($http = array_search('HTTP', $methods)) !== false)
                array_splice($methods, $http, 1, array('GET','HEAD','POST','PUT','DELETE','TRACE','OPTIONS','CONNECT','PATCH'));

            $this->methods      = $methods;

        }


        /**
         * Get a non-transformed route property
         * @param string    $property       Property's name
        **/
        public function __get($property) {
            if(!isset($this->$property))
                trigger_error('Undefined route property `'.$property.'`', E_USER_ERROR);

            return $this->$property;
        }


        /**
         * Retrieve route URL
         * @param array      $parameters         Route parameters
         * @param string     $domain             Specify a route domain, if not defined by the route
        **/
        public function getURL(array $parameters = null, $domain = null, $scheme = null) {

            $domain = (!empty($domain) ? $domain : $this->domain);

            $uri = $this->pattern;
            foreach($this->parameters as $name => $regexp) {

                if(!isset($parameters[$name]))
                    trigger_error('Missing parameter "'. $name .'"', E_USER_ERROR);

                if(!preg_match('#^'.$regexp.'$#isU', $parameters[$name]))
                    trigger_error('Parameter "'. $name .'" doesn\'t match the REGEXP', E_USER_ERROR);

                $domain = mb_str_replace('{'.$name.'}', $parameters[$name], $domain);
                $uri    = mb_str_replace('{'.$name.'}', $parameters[$name], $uri);
            }

            if(!empty($domain)) $uri = '//'.$domain.$uri;
            if(!empty($scheme)) $uri =  $scheme.':'.$uri;

            return $uri;

        }


    }
