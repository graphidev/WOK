<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Application;

    /**
     * The Services class manages services collection.
     * It allows to add, get, and remove services.
    **/
    class Services {

        /**
         * @var object  $collection  Services collection
        **/
        private $collection;

        /**
         * @var object  $_instance  Service instance
        **/
        private static $_instance;


        /**
         * Instanciate services object for static usage
        **/
        public function __construct() {
            if(!self::$_instance)
                self::$_instance = $this;
        }


        /**
         * Allow static instance call
        **/
        public static function getInstance() {
            if(!self::$_instance)
                self::$_instance = self::__construct();

            return self::$_instance;
        }


        /**
         * Register a new service
         * @param  string           $name       Name under which the service will be recorded
         * @param  Closure|object   $service    Service to record as an object or a closure
        **/
        public function register($name, $service) {
            $this->collection[$name] = $service;
        }

        /**
         * Remove an existing service
         * @param string    $service        Service's name to remove
        **/
        public function remove($service) {
            if($this->has($service))
                unset($this->collection[$service]);
        }

        /**
         * Check the availability of a service
         * @param   string    $name           Service's name
         * @return  boolean   Returns wether the service is registered or not
        **/
        public function has($service) {
            return isset($this->collection[$service]);
        }


        /**
         * Get a specific service
         * @param   string    $name           Service's name
         * @param   string    $parameters     Initialisation service parameters
         * @return  boolean   Returns the service or false
        **/
        public function get($service, array $parameters = array()) {

            if(!$this->has($service))
                trigger_error('Service "' . $service . '" is not registered', E_USER_ERROR);


            if(is_closure($this->collection[$service])) {

                /*
                $reflection = new ReflectionFunction( $this->collection[$service] );
                if(count($parameters) < $reflection->getNumberOfRequiredParameters())
                    trigger_error('Some of "'.$service.'" instanciation parameters are missing', E_USER_ERROR);
                */

                $instance = call_user_func_array($this->collection[$service], $parameters);
            }

            elseif(is_string($this->collection[$service])) {

                if(!class_exists($this->collection[$service], true))
                    trigger_error('Service '.$service.' is not a callable class', E_USER_);

                /*
                $reflection = new ReflectionMethod($this->collection[$service], '__construct');
                if(count($parameters) < $reflection->getNumberOfRequiredParameters())
                    trigger_error('Some of "'.$service.'" instanciation parameters are missing', E_USER_ERROR);
                */

                $class = new \ReflectionClass($this->collection[$service]);
                $instance = $class->newInstanceArgs($parameters);
            }

            elseif(is_object($this->collection[$service])) {
                $instance = $this->collection[$service];
            }

            else {
                trigger_error('Service "'.$service.'" not callable', E_USER_ERROR);
            }

            return $instance;

        }


        /**
         * Check if a service instance exists
         * @param string    $service        Service name
        **/
        public function __isset($service) {
            return isset($this->collection[$service]);
        }


        /**
         * Get a service instance without parameters
         * @param string    $service        Service name
        **/
        public function __get($service) {
            if(!isset($this->collection[$service]))
                trigger_error('Undefined service '.$service, E_USER_ERROR);

            return $this->get($service);
        }

        /**
         * Get a service instance (shortcut)
         * @param string    $service        Service name
        **/
        public function __call($service, array $arguments) {
            if(!isset($this->collection[$service]))
                trigger_error('Undefined service '.$service, E_USER_ERROR);

            return $this->get($service, $arguments);
        }

        /**
         * destroy every service
        **/
        public function __destruct() {

            foreach($this->collection as $name => $service) {
                unset($this->collection[$name]);
            }

        }


    }
