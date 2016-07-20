<?php


        /**
         * Web Operational Kit
         * The neither huger nor micro humble framework
         *
         * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
         * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
         * @license     BSD <license.txt>
        **/

        namespace Command\Components;

        /**
         * The Arguments class provides an interface
         * to manage command lines parameters.
        **/
        class Arguments {

            protected $arguments = array();

            /**
             * Instanciante arguments
             * @param array     $arguments      Arguments list
            **/
            public function __construct(array $arguments = array()) {
                $this->arguments = $arguments;
            }


            /**
             * Check if an argument is available
             * @param string    $name        Argument's name
            **/
            public function hasArgument($name) {
                return isset($this->arguments[$name]);
            }

            /**
             * Get an argument value
             * @param string    $name           Argument's name
             * @param mixed     $default        Default argument's value
            **/
            public function getArgument($name, $default = false) {

                if(!$this->hasArgument($name))
                    return $default;

                return $this->arguments[$name];

            }


            /**
             * Add an argument value
             * @param string    $name        Argument's name
             * @param mixed     $value       Argument's value
            **/
            public function addArgument($name, $value) {

                $argument = $this->getArgument($name);

                if($argument) {

                    if(is_array($argument)) {
                        $argument[] = $value;

                    }
                    else {
                        $argument = array($argument, $value);
                    }

                    $value = $argument;

                }

                $this->setArgument($name, $value);

            }

            /**
             * Set an argument value
             * @param string    $name        Argument's name
             * @param mixed     $value       Argument's value
            **/
            public function setArgument($name, $value) {
                $this->arguments[$name] = $value;
            }


            /**
             * Get all arguments as array
            **/
            public function getAll() {
                return $this->arguments;
            }

        }
