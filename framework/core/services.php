<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Framework\Core;
    use Framework\Utils\Collection;

    /**
     * The Services class manages services collection.
     * It allows to add, get, and remove services.
     * @package Framework/Utils
    **/
    class Services {

        /**
         * @var object   Services collection
        **/
        private $collection;


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
                trigger_error('Service "' . $service . '" not registered', E_USER_ERROR);

            if(is_closure($this->collection[$service])) {
                return call_user_func_array($this->collection[$service], $parameters);
            }

            elseif(is_string($this->collection[$service])) {

                if(is_string($this->collection[$service]) && !class_exists($this->collection[$service], true))
                    trigger_error('Service '.$service.' is not a callable class', E_USER_);

                $class = new \ReflectionClass($this->collection[$service]);
                return $class->newInstanceArgs($parameters);
            }

            return $this->collection[$service];

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
