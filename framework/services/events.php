<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Framework\Services;


    /**
     * Allow to define and call events
     * during the run time process.
    **/
    class Events {

        /**
         * @var array $events      Events calback collection
        **/
        private $events     = array();

        /**
         * Register a callback to a given event
         * @param   string    $name         Event name
         * @param   Closure   $callaback    Event callback
        **/
        public function register($name, \Closure $callback) {
            $this->events[$name][] = $callback;
        }


        /**
         * Fire an event
         * @param   string      $name           Event name
         * @param   array       $parameters     Callback parameters
        **/
        public function trigger($name, array &$parameters = array()) {

            if(empty($this->events[$name]))
                return;

            foreach($this->events[$name] as $event) {
                call_user_func_array($this->events[$name], $parameters);
            }

        }


        /**
         * Remove an event listener
         * @param string   $name    Event name
        **/
        public function remove($name) {

            if(isset($this->events[$name]))
                unset($this->events[$name]);

        }


    }
