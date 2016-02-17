<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Console;

    use Components\Log;
    use Components\Backtrace;
    use \Console\Adapters\AdapterInterface;


    /**
     * The Console class provides
     * an interface to define
     * and register logs
    **/
    class Console {


        /**
         * @var $adapter        Logger adapter
        **/
        protected $adapter;

        /**
         * @var $logs           Logs collection
        **/
        protected $logs         = array();


        /**
         * Instanciante console adapter
         * @param AdapterInterface      $adapter        Logger adapter
        **/
        public function __construct(AdapterInterface $adapter) {

            $this->adapter = $adapter;

        }


        /**
         * Define a log
         * @param   string          $message        Log message
         * @param   backtrace       $backtrace      Log message
        **/
        public function log($message, Backtrace $backtrace = null) {

            if(empty($backtrace)) {
                $backtrace = new Backtrace(1);
            }

            $this->logs[] = new Log($message, $backtrace);

        }


        /**
         * Register logs at the end
        **/
        public function __destruct() {

            foreach($this->logs as $log) {
                $this->adapters->register($log);
            }

        }


    }
