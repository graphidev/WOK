<?php

    class App {

        private $entrypoint;
        private $shutdown;
        private $errors         = array();

        const ERR_ERROR         = 'ERROR';          // Something have to be revised
        const ERR_WARNING       = 'WARNING';        // Something should be revised
        const ERR_NOTICE        = 'NOTICE';         // See ERR_WARNING
        const ERR_DEPRECATED    = 'DEPRECATED';     // Something should not be used anymore
        const ERR_EXCEPTION     = 'EXCEPTION';      // Exception that have not been catched

        public static function init($entrypoint) {

            $this->entrypoint = $entrypoint;

        }

        /**
         * Set an error shutdown callback
         * @param   Closure     $callback       Callback closure definition
         * @example $app->shutdown(function($errors) { ... });
        **/
        public function shutdown(Closure $callback) {
            $this->shutdown = $callback;
        }
        
        /**
         * Parse error for registration
         * @param   integer     $type       User error type code
         * @param   string      $message    Associated error message
         * @param   string      $file       Concerned file by the error
         * @param   integer     $line       Line where the error occured in the file
        **/
        private function parseError($type, $message, $file, $line) {

            switch ($type) {
                case E_USER_ERROR:
                    $type = self::ERR_ERROR;
                    break;

                case E_USER_WARNING:
                    $type = self::ERR_WARNING;
                    break;

                case E_USER_NOTICE:
                    $type = self::ERR_NOTICE;
                    break;

                case E_USER_DEPRECATED:
                    $type = self::ERR_DEPRECATED;
                    break;

                default:
                   $type = self::ERR_ERROR;
            }

            $this->errors[] array(
                'type' => $type,
                'message' => $message,
                'file' => $file,
                'line' => $line
            );

            // Register item
            Console::log('errors', '['.$type.'] '.$message.' - '.$file.' on line '.$line);

        }

        /**
         * Run the action and output status;
        **/
        public function run(Closure $action) {

            // Define reporting tools
            error_reporting( ERROR_REPORTING_LEVEL );

            // Handle errors
            set_error_handler(function($type, $message, $file, $line) {
                if(!(error_reporting() & $type)) return;

                $this->parseError($type, $message, $file, $line);

                return !SYSTEM_DEBUG; // Show/hide error message
            });

            // Handle not catched exceptions
            set_exception_handler(function($e) {
                trigger_error('Exception '.get_class($e).' not catched : '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine(), E_USER_ERROR);
            });

            // Start buffering
            ob_start();

            // Register shutdown
            register_shutdown_function(function() use($this) {

                if(($error = error_get_last()) && ($total = count($this->errors)) && $error != $this->errors[$total-1])
                    $this->parseError($error['type'], $error['message'], $error['file'], $error['line']);

                Console::register();

                // Build new response
                if( is_closure($this->shutdown) && !empty($this->errors) ) {
                    ob_clean();
                    exit( call_user_func($this->shutdown, $this->errors) );
                }

            });

            $response = call_user_func($action, $this->entrypoint);

            // Shutdown on error
            if( !empty($this->errors) );
                exit;

            // Stop buffering and output
            ob_end_flush();

            // Send status
            exit($response);

        }



    }
