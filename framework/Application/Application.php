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
     * The Application class define methods
     * used to run the framework environment
    **/
    class Application {

        /**
         * @var $services      Services collection object
        **/
        private $services;


        /**
         * @var $before        Before middleware interaction
        **/
        private $before;


        /**
         * @var $action        Main middleware interaction
        **/
        private $action;


        /**
         * @var $after        Before middleware interaction
        **/
        private $after;


        /**
         * Initialize object :
         * Define services and initialize self destruction
         * @param Framework\Core\Services  $services     Services collection
        **/
        public function __construct(Services &$services) {

            $this->services = $services;

            // Self destruction on script end
            register_shutdown_function(function($self) {
                $self->__destruct();
            }, $this);

        }


        /**
         * Define the main middleware action
         * @param Closure  $action     Action to execute in the application
        **/
        public function action(\Closure $action) {

            $this->action = $action;

        }


        /**
         * Define the before middleware action
         * @param Closure  $action     Action to execute in the application
        **/
        public function before(\Closure $action) {

            $this->before = $action;

        }


        /**
         * Define the before middleware action
         * @param Closure  $action     Action to execute in the application
        **/
        public function after(\Closure $action) {

            $this->after = $action;

        }


        /**
         * Execute an action as far as possible
         * @param   Closure     $action           The action to execute
        **/
        protected function exec(\Closure $action) {

            while(is_callable($action)) {
               $action = call_user_func($action, $this->services);
            }

            return $action;

        }


        /**
         * Run middleware as a logical order
         * @note this method send the end signal
        **/
        public function run() {

            // Apply the before middleware event
            if(!empty($this->before)) {

                $before = $this->exec($this->before);

                if(!is_null($before))
                    $output = $before;

            }

            // Execute the main middleware action
            if(!isset($output))
                $output = $this->exec($this->action);

            // Apply the after middleware
            if(!empty($this->after)) {

                $after = $this->exec($this->after);

                if(!is_null($after))
                    $output = $after;

            }

            exit (is_null($output) ? 0 : $output);

        }


        /**
         * Object destruction :
         * Destroy services
        **/
        public function __destruct() {
            unset($this->services);
        }

    }
