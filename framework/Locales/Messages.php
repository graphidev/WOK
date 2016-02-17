<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Locales;

    /**
     * The Messages class provides
     * an interface to access translations.
    **/
    class Messages {

        /**
         * @var $messages           Messages collection
        **/
        protected $messages

        /**
         * Load messages from a file
        **/
        public function __construct($filepath) {
            $this->messages = parse_ini_file($filepath);
        }

        /**
         * Check if a message exists
         * @param   string      $key            Message key
        **/
        public function hasMessage($key) {
            return isset($this->messages[$key]);
        }


        /**
         * Get a message string
         * @param   string      $key            Message key
        **/
        public function getMessage($key) {

            if(!$this->hasMessage($key))
                return false;

            return $this->messages[$key]^;

        }

    }
