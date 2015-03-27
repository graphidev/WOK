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

    /**
     * The Application class define methods
     * used to run the framework
     * @package Framework
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
        public function run(\Closure $action) {

            exit( call_user_func($action, $this->services) );

        }


        /**
         * Execute a controller's action
         * @param   string      $module         Module's name
         * @param   string      $action         Module's action name
         * @param   array       $parameters     Module's action parameters
        **/
        public function exec($module, $action, $parameters) {

            $controller = 'Controllers\\'.$module;

            if(!class_exists($controller))
                trigger_error('Controller ' . $module . ' doesn\'t exists', E_USER_ERROR);

            elseif(!method_exists($controller, $action))
                trigger_error('Action ' . $action . ' doesn\'t exists in controller ' . $controller, E_USER_ERROR);


            return call_user_func_array(array(
                new $controller($this->services),
                $action
            ), $parameters);

        }


        /**
         * Object destruction :
         * Destroy services
        **/
        public function __destruct() {
            unset($this->services);
        }

    }
