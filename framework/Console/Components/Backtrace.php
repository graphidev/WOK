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
     * The Backtrace class provides
     * an interface to access the
     * log backtrace (for debugging)
    **/
    class Backtrace {

        /**
         * @const   IGNORE_CLOSURES             Prevent registering closures as backtrace
        **/
        const IGNORE_CLOSURES           = 0x01;

        /**
         * @const   IGNORE_CLOSURES             Prevent registering triggered errors as backtrace
        **/
        const IGNORE_TRIGGERED_ERRORS   = 0x02;


        /**
         * @var $caller             Trace caller function
        **/
        protected $caller;

        /**
         * @var $file               Trace caller file
        **/
        protected $file;

        /**
         * @var $line               Trace caller file line
        **/
        protected $line;

        /**
         * @var $backtrace          Backtrace collection
        **/
        protected $backtrace;


        /**
         * Instanciate a backtrace object
         * @param   integer     $unstack           Unstacking backtraces number
        **/
        public function __construct($unstack = 0, $options = null) {

            $unstack++;
            $traces = debug_backtrace();
            $traces = array_slice($traces, $unstack);
            $traces = array_reverse($traces);

            // Filter traces with options
            if(!empty($options)) {

                foreach($traces as $key => $t) {

                    if(empty($t['class']) && $t['function'] == 'trigger_error' && ($options & self::IGNORE_TRIGGERED_ERRORS))  {
                        unset($traces[$key]);
                    }
                    elseif($t['function'] == '{closure}' && ($options & self::IGNORE_CLOSURES)) {
                        unset($traces[$key]);
                    }
                    else {
                        break;
                    }

                }

            }

            $backtrace = array_shift($traces);

            if(isset($backtrace['class'])) {
                $this->caller = $backtrace['class'].$backtrace['type'].$backtrace['function'];
            }
            else {
                $this->caller = mb_str_replace('{closure}', 'Closure', $backtrace['function']);
            }

            $this->file         = (isset($backtrace['file']) ? $backtrace['file'] : '');
            $this->line         = (isset($backtrace['line']) ? $backtrace['line'] : '');
            $this->backtrace    = $traces;

        }


        /**
         * Get the trace caller
        **/
        public function getCaller() {
            return $this->caller;
        }


        /**
         * Get the trace file
        **/
        public function getFile() {
            return $this->file;
        }


        /**
         * Get the trace line
        **/
        public function getLine() {
            return $this->file;
        }


        /**
         * Get the rest of the backtrace
        **/
        public function getTrace() {
            return $this->backtrace;
        }

    }
