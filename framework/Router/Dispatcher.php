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
     * The Dispatcher class provide a way
     * to manipulate the current route action
    **/
    class Dispatcher {

        /**
         * @var $controller         Action parent controller
        **/
        protected $controller;

        /**
         * @var $action             Action method name
        **/
        protected $action;

        /**
         * @var $action             Action method name
        **/
        protected $parameters       = array();

        /**
         * Instanciate the dispatcher instance
         * @param       string          $controller             Action controller path
         * @param       string          $action                 Action method name
         * @param       array           $parameters             Action given parameters
        **/
        public function __construct($controller, $action, array $parameters = array()) {

            $this->controller   = $controller;
            $this->action       = $action;
            $this->parameters   = $parameters;

        }

        /**
         * Get the action controller path
        **/
        public function getController() {
            return $this->controller;
        }

        /**
         * Get the action method name
        **/
        public function getAction() {
            return $this->action;
        }

        /**
         * Get the action parameters
        **/
        public function getParameters() {
            return $this->parameters;
        }


        public function __invoke() {

            if(!class_exists($this->controller))
                trigger_error('Undefined Controller '.$this->controller, E_USER_ERROR);

            $reflection =  new \ReflectionClass($this->controller);
            $class      = $reflection->newInstanceArgs(func_get_args());

            if(!method_exists($this->controller, $this->action))
                trigger_error('Undefined Action '.$this->controller.'::'.$this->action, E_USER_ERROR);


            $output = call_user_func(array($class, $this->action), $this->parameters);

            unset($class); // Call the class destructor

            return $output;

        }


    }
