<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Command;


    use Components\Arguments;

    /**
     * The Command class provides an interface
     * to manage CLI interactions
    **/
    class Command extends \Iterator {

        /**
         * @var $arguments          Arguments collection
        **/
        protected $arguments;

        /**
         * Instanciate the command class
        **/
        public function __construct() {
            $this->arguments = new Arguments();
        }

        /**
         * Instanciate a new argument
         * @param   string      $key        Argument single character key ( -k usage )
         * @param   string      $name       Argument both name & multi characters key ( --key usage )
         * @param   string      $mode       Argument value mode
         * @param   string      $helper     Argument description helper
        **/
        public function withArgument($key, $name, $mode = Arguments::VALUE_IGNORED, $helper = null) {
            $this->arguments->addArgument($key, $name, $mode, $helper);
        }


        /**
         * Check if an argument have been instanciated
         * @param   string      $name       Argument name
        **/
        public function hasArgument($name) {
            return $this->arguments->hasArgument($name);
        }


        /**
         * Get an argument
         * @param   string      $name          Argument name
         * @param   mixed       $default       Argument default value
        **/
        public function getArgument($name, $default = false) {

            return $this->arguments->getArgument($name, $default);

        }


    }
