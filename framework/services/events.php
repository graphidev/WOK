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

    class Events {

        private $events     = array();

        public function register($name, $callback) {
            $this->events[$name] = $callback;
        }

        public function remove($name) {
            if(isset($this->events[$name]))

            unset($this->events[$name]);
        }

        public function trigger($name, $parameters = array()) {

            if(!isset($this->events[$name]))
                trigger_error('Undefined event '.$name.' callback', E_USER_ERROR);

            call_user_func_array($this->events[$name], $parameters);

        }


    }
