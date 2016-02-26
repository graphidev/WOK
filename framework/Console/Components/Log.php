<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Console\Components;


    /**
     * The Log class provides
     * an interface to instanciante
     * and access a log data
    **/
    class Log {

        protected $message;

        protected $time;

        protected $backtrace;

        /**
         * Instanciante a new log
         * @param   string          $message        Log message
         * @param   Backtrace       $backtrace      Log backtrace
        **/
        public function __construct($message, Backtrace $backtrace) {

            $this->time = time();
            $this->message = $message;
            $this->backtrace = $backtrace;

        }

        /**
         * Get the log formated date
         * @param   string      $format         Date format (using http://php.net/date first parameter)
        **/
        public function getDate($format = 'H:i:s') {
            return date($format, $this->time);
        }


        /**
         * Get the log message
        **/
        public function getMessage() {
            return $this->message;
        }

        /**
         * Get the log backtrace
        **/
        public function getBacktrace() {
            return $this->backtrace;
        }


        /**
         * Get the default log string formated value.
         * @note Default format : [{date|Y-m-d H:i:s}] {Message} | {Backtrace:Method} {Backtrace:File} {Backtrace:Line}
        **/
        public function __toString() {

            $string  = '['.$this->getDate('H:i:s').'] '.$this->message;
            $string .= ' | ['.$this->backtrace->getCaller().'] '. $this->backtrace->getFile().':'.$this->backtrace->getLine();

            return $string;

        }


    }
