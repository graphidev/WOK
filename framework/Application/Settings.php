<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <licence.txt>
    **/

    namespace Application;


    class Settings {


        protected $settings;


        /**
         * Instanciate the settings collection
        **/
        public function __construct(array $settings) {
            $this->settings = $settings;
        }


        /**
         * Allow settings properties existence check
         * @param   string      $property       Settings property name
        **/
        public function __isset($property) {

            return isset($this->$property);

        }


        /**
         * Allow settings properties access
         * @param   string      $property       Settings property name
        **/
        public function __get($property) {

            if(!isset($this->settings[$property])) {
                trigger_error('Undefined configuration constant "'.$property.'"', E_USER_ERROR);
            }

            return $this->settings[$property];

        }


    }
