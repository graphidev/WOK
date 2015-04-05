<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Framework\Services;

    /**
     * The Console service allow you to manage
     * errors and logs information before registering them.
    **/
    class Console {

        /**
         * @var $logs   Logs collection
        **/
        private $logs   = array();

        /**
         * @var $errors     Errors collection
        **/
        private $errors  = array();

        /**
         * @var $handlers     Errors handlers callbacks
        **/
        private $handlers  = array();

        /**
         * @var $format       Log format
        **/
        private $format    = '[:type] - :message | :file : :line';

        /**
         * @const   PATH_LOGS       Relative logs path
        **/
        const PATH_LOGS         = '/storage/logs';

        const LOG_DEFAULT       = 'DEFAULT';        // Default log (undefined type)
        const LOG_ERROR         = 'ERROR';          // Something have to be revised : 503 response
        const LOG_WARNING       = 'WARNING';        // Something should be revised
        const LOG_NOTICE        = 'NOTICE';         // See WARNING
        const LOG_DEPRECATED    = 'DEPRECATED';     // Something should not be used anymore
        const LOG_EXCEPTION     = 'EXCEPTION';      // Exception that have not been catched


        /**
         * Initialize logs and reporting callbacks
         * @param int   $level      Error handling level
        **/
        public function __construct($level = E_ALL, $hidden = false) {

            // Set log format
            if(!empty($format))
                $this->format = $format;

            // Define reporting tools
            error_reporting( $level );

            // Handle errors
            set_error_handler(function($type, $message, $file, $line) use($hidden){
                if(!(error_reporting() & $type)) return;


                // Get real error caller from backtrace
                $backtrace = debug_backtrace();
                $backtrace = array_slice($backtrace, 1);
                $caller = current($backtrace);

                if($caller['function'] == 'trigger_error') {

                    foreach($backtrace as $key => $contexte) {
            			if(empty($contexte['class']) && $contexte['function'] == 'trigger_error')
            				unset($backtrace[$key]);

            			else
            				break;
            		}

                    $caller = current($backtrace);
                    $file = (!empty($caller['file']) ? $caller['file'] : $file);
                    $line = (!empty($caller['line']) ? $caller['line'] : $line);

                }

                if(isset($this->handlers[$type]))
                    $prevent = call_user_func($this->handlers[$type], $message, $file, $line);

                if(!isset($prevent) || !$prevent) {
                    $this->log($message, $type, $file, $line);

                    if(!$hidden) { // Display errors
                        $prevent = true;
                        echo $this->parse($this->getType($type), $message, $file, $line) . PHP_EOL;
                        if($type == 'ERROR') exit;
                    }

                }

                // Disable/enable built-in behavior (showing error)
                return (!empty($prevent) ? true : $hidden);

            });

            // Handle not catched exceptions (see error handler)
            set_exception_handler(function($e) {
                trigger_error('Exception '.get_class($e).' not catched : '
                    .$e->getMessage().' in '.$e->getFile()
                    .' on line '.$e->getLine(), E_USER_ERROR);
            });

            // Register errors shutdown callbacks
            register_shutdown_function(function($this) {

                /*
                if(($error = error_get_last()) && ($total = count($this->logs)) && $error != $this->logs[$total-1])
                    $this->log($error['message'], $error['type'], $error['file'], $error['line']);
                */

                if( !empty($this->errors) ) {
                    $this->register();

                    // Custom callback
                    if(!SYSTEM_DEBUG && is_closure($this->shutdown)) {
                        ob_clean();
                        exit( call_user_func($this->shutdown, $this->errors) );
                    }
                }

            }, $this);

            // Start buffering
            ob_start();
        }

        /**
         * De
        **/

        /**
         * Register a log. Also can define an error (type)
         * @param   string      $message     Log message
         * @param   string      $type        Log type (E_* or custom)
         * @param   string      $file        Generated log file
         * @param   string      $line        Generated log file line
        **/
        public function log($message, $type = self::LOG_NOTICE, $file = null, $line = null) {

            if(!is_string($type))
                $type = $this->getType($type);

            if(empty($file) || empty($line)) {
                $backtrace = debug_backtrace();
                $backtrace = array_shift($backtrace);
                $file = (!empty($file) ? $file : $backtrace['file']);
                $line = (!empty($line) ? $line : $backtrace['line']);
            }


            $log = array(
                'message'   => $message,
                'type'      => $type,
                'file'      => $file,
                'line'      => $line
            );

            $this->logs[] = $log;

            // Save as error
            if(in_array($type, array(self::LOG_WARNING, self::LOG_ERROR)))
                $this->errors = $log;
        }


        /**
         * Get a string type from PHP error types
         * @param   string     $type    PHP error type
         * @return  Returns the textual type
        **/
        private function getType($type) {

            switch ($type) {
                case E_USER_ERROR:
                    $type = self::LOG_ERROR;
                    break;

                case E_USER_WARNING:
                    $type = self::LOG_WARNING;
                    break;

                case E_USER_NOTICE:
                    $type = self::LOG_NOTICE;
                    break;

                case E_USER_DEPRECATED:
                    $type = self::LOG_DEPRECATED;
                    break;

                default:
                   $type = (is_string($type) ? $type : self::LOG_ERROR);
            }

            return $type;
        }

        /**
         * Parse a log from the defined format
         * @param   string      $type        Log type
         * @param   string      $message     Log message
         * @param   string      $file        Generated log file
         * @param   string      $line        Generated log file line
        **/
        private function parse($type, $message, $file, $line) {

            $log = str_replace(
                // Variables to replace
                array(
                    ':type',
                    ':message',
                    ':file',
                    ':line'
                ),

                // Replacement values
                array(
                    $type,
                    $message,
                    $file,
                    $line
                ),

                // Original format
                $this->format
            );

            return $log;
        }


        /**
         * Set an error shutdown callback
         * @param   Closure     $callback       Callback closure definition
         * @example $app->shutdown(function($errors) { ... });
        **/
        public function shutdown(\Closure $callback) {
            $this->shutdown = $callback;
        }


        /**
         * Set an error handler
         * @param   string      $type           Error type to handle
         * @param   Closure     $callback       Error callback
        **/
        public function setHandler($type, \Closure $callback) {
            $this->handlers[$type] = $callback;
        }


        /**
         * Define a log format
         * @note Please be careful redefining the log format. This could disable the interest of this class
         * @param string    $format     The log format containing ":type", ":message", ":file" and ":line"
        **/
        public function setLogFormat($format) {
            $this->format = $format;
        }


        /**
         * Register logs collection as file
        **/
        private function register() {

            mkpath($path = root(self::PATH_LOGS));
            $file = fopen($path . date('/Y-m-d').'.log', 'a+');

            foreach($this->logs as $i => $log) {

                $log = $this->parse($log['type'], $log['message'], $log['file'], $log['line']);
                $row = '['.date('H:i:s').'] - '. $log . PHP_EOL;

                fwrite($file, $row);

            }

            fclose($file);
        }


        /**
         * Clean  buffer and output response.
         * Also registers logs
        **/
        private function __destroy () {

            // Shutdown on error
            if( !empty($this->errors) );
                exit;

            $this->register();

            // Stop buffering and output
            ob_end_flush();

        }

    }
