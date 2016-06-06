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
     * The abstract Console AdapterInterface
     * force a context usage of the logs
     * management.
    **/
    abstract class AdapterInterface {

        /**
         * Register a specific log
         * @param   Log         $log            Log interface
        **/
        abstract public function log(Log $log);

        /**
         * Register a logs collection
         * @param   array        $logs            Logs interfaces collection
        **/
        abstract public function register(array $logs);

    }
