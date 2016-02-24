<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Console\Adapters;

    use \Console\Components\Log;


    /**
     * The Files class provides
     * an adapter for the Console class.
    **/
    class Files extends AdapterInterface {

        /**
         * @var    $storage             Logs storage path
        **/
        protected $storage;


        /**
         * @var     $extension          Logs file extension
        **/
        protected $extension;

        /**
         * Instanciate files logging console adapter
        **/
        public function __construct($storage, $extension = 'log') {
            $this->storage      = $storage;
            $this->extension    = $extension;
        }


        /**
         * This method is supposed to register log by log.
         * However, for performance reasons, all the logs must be registered
         * with the register method.
         * @param   Log   $log      Log interface
        **/
        public function log(Log $log) {}


        /**
         * Register logs in a file
         * @param array     $logs       Logs interfaces collection
        **/
        public function register(array $logs) {

            mkpath($this->storage);

            $file = fopen($this->storage . date('/Y-m-d').'.'.$this->extension, 'a+');

            foreach($logs as $i => $log) {
                $string = (string) $log;
                fwrite($file, $string . PHP_EOL);
            }

            fclose($file);

        }


    }
