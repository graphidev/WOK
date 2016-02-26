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
     * The Arguments class provides an
     * interface to manage command arguments
    **/
    class Arguments extends \Iterator {

        /**
         * @const VALUE_IGNORED             Ignore argument value
        **/
        const VALUE_IGNORED      = '';

        /**
         * @const VALUE_REQUIRED            Only define value when it is set
        **/
        const VALUE_REQUIRED     = ':';

        /**
         * @const VALUE_OPTIONNAL           Allow argument access even when the value is no set
        **/
        const VALUE_OPTIONNAL    = '::';


        /**
         * @var $arguments          Arguments collection
        **/
        protected $arguments        = array();


        /**
         * Instanciate a new argument
         * @param   string      $key        Argument single character key ( -k usage )
         * @param   string      $name       Argument both name & multi characters key ( --key usage )
         * @param   string      $mode       Argument value mode
         * @param   string      $helper     Argument description helper
        **/
        public function addArgument($key, $name, $mode = self::VALUE_IGNORED, $helper = null) {
            $this->arguments[$name] = (object) array(
                'mode'      => $mode,
                'shortkey'  => $key,
                'longterm'  => $name,
                'helper'    => $helper,
                'value'     => null,
            );
        }


        /**
         * Set an argument value
         * @param   string      $name       Argument name
         * @param   mixed      value        Argument's value
        **/
        public function setArgument($name, $value) {

            if(!$this->hasArgument($name))
                throw new \OutOfBoundsException('Undefined argument '.$name);

            $this->arguments[$name]->value = $value;

        }


        /**
         * Check if an argument have been instanciated
         * @param   string      $name       Argument name
        **/
        public function hasArgument($name) {
            return isset($this->arguments[$name]);
        }


        /**
         * Get an argument
         * @param   string      $name       Argument name
        **/
        public function getArgument($name, $default = false) {

            if(!$this->hasArgument($name))
                throw new \OutOfBoundsException('Undefined argument '.$name);

            $value = $this->arguments[$name];

            if(!$value)
                $value = $default;

            return $value;

        }


    }
