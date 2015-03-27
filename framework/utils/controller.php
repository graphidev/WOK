<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/
    namespace Framework\Utils;

    /**
     * The Controller class define global services
     * for specific controllers such as entry point
     * and custom services
    **/
    class Controller {

        protected $module     = null;
        protected $services;

        /**
         * Define usable services and module
         * @param Class     $services        Services collection
         * @param string    $module          Current route module
        **/
        public function __construct(\Framework\Core\Services $services, $module = null) {
            $this->module = $module;
            $this->services = $services;
        }

    }
