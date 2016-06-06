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

    /**
     * The Command class provides an interface
     * to access command lines instructions and parameters.
    **/
    class Command {

        protected $path;
        protected $arguments    = array();


        /**
         * Parse command line arguments
         * @param   array       $argv       Command line splitted instructions
        **/
        public function __construct(array $argv) {

            foreach($argv as $arg) {

                if(mb_substr($arg, 0, $lenght = mb_strlen($prefix = '--')) == $prefix) {

                    $arg = mb_substr($arg, $lenght);

                    if($pos = mb_strpos($arg, '=')) {

                        $name   = mb_substr($arg, 0, $pos);
                        $value  = mb_substr($arg, $pos+1);

                        $delimiter = mb_substr($value, 0, 1);
                        if(in_array($delimiter, ['"', '"']) && mb_substr($value, -1) == $delimiter) {
                            $value = str_replace('\\'.$delimiter, $delimiter, $value);
                            $value = mb_substr($value, 1, mb_strlen($value)-1);
                        }

                        $this->addArgument($name, $value);

                    }
                    else {
                        $this->addArgument($arg, true);
                    }

                }
                elseif(empty($this->path)) {
                    $this->path = $arg;
                }

            }

        }


        /**
         * Get the command path
        **/
        public function getPath() {
            return $this->path;
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



    }
