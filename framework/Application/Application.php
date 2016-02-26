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
         * @var object      Services collection object
        **/
        private $services;


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
         * Run the application
         * @param Closure  $action     Action to execute in the application
        **/
        public function action(\Closure $action) {

            $output = call_user_func($action, $this->services);

            while(is_callable($output)) {
               $output = call_user_func($output, $this->services);
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
