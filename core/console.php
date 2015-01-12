<?php
    
    /** 
     * Manage errors and not catched exceptions.
     * It will register them as logs files (one per day)
     *
     * @package Core
    **/

    class Console {
        
        /**
         * Logs as array (saved on register)
         * and custome error handlers
        **/
        private static $logs        = array();
        private static $handlers    = array();
        private static $exceptions  = array();
        private static $format      = '[:time] [:type] :message in :file on line :line';
        
        /**
         * Reporting level
        **/
        const REPORTING_LEVEL       = E_ALL;
        
        /**
         * Logs types
        **/
        const LOG_DEFAULT       = 'DEFAULT';        // Default log (undefined type)
        const LOG_ERROR         = 'ERROR';          // Something have to be revised : 503 response
        const LOG_WARNING       = 'WARNING';        // Something should be revised
        const LOG_NOTICE        = 'NOTICE';         // See WARNING
        const LOG_DEPRECATED    = 'DEPRECATED';     // Something should not be used anymore
        const LOG_EXCEPTION     = 'EXCEPTION';      // Exception that have not been catched
        
        
        /**
         * Redefine logs format
         * @param   integer     $level      The errors level reporting
        **/
        public static function init($level = Console::REPORTING_LEVEL) {
            
            // Define error reporting level
            error_reporting($level);

            // Handle errors
            set_error_handler(function($type, $message, $file, $line) {                 
                if(!(error_reporting() & $type)) return;

                self::parseError($type, $message, $file, $line);
                 
                return !SYSTEM_DEBUG; // Show/hide error message
                
            });
            
            // Handle not catched exceptions
            set_exception_handler(function($e) {
                                
                foreach(self::$exceptions as $name => $callback) {
                    if(is_string($name) && is_a($e, $name)) {                        
                        $prevent = call_user_func($callback, $name);
                        break;
                    }
                }
                
                if(!isset($prevent) || !$prevent)
                    trigger_error(get_class($e).' not catched : '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine(), E_USER_ERROR);
                
            });
        }
        
        /**
         * Parse error for registration
         * @param   integer     $type       User error type code
         * @param   string      $message    Associated error message
         * @param   string      $file       Concerned file by the error
         * @param   integer     $line       Line where the error occured in the file
        **/
        private static function parseError($type, $message, $file, $line) {
            
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
                   $type = self::LOG_ERROR;
            }

            if(isset(self::$handlers[$type])) 
                $prevent = call_user_func(self::$handlers[$type], $message, $file, $line);
            
            if(!isset($prevent) || !$prevent)
               self::log($message, $type, $file, $line);

        }
        
        /**
         * Set a custom error callback
         * @param mixed     $level      Error's level to catch
         * @param Closure   $callback   Fallback for this error
        **/
        public static function catchError($level, Closure $callback) {
            self::$handlers[$level] = $callback;  
        }
        
        /**
         * Catch custom exceptions
         * @param Closure $callback     Exception fallback closure
         * @param string  $name         Exception's name that you want to catch
        **/ 
        public static function catchException($callback, $name = 'Exception') {
            self::$exceptions[$name] = $callback;
        }
        
        /**
         * Add a log
         * @param string    $message
         * @param string    $type
         * @param string    $file
         * @param integer   $line
        **/
        public static function log($message, $type = self::LOG_WARNING, $file = null, $line = null) {
            
            $backtrace = debug_backtrace();
            $backtrace = array_shift($backtrace);

            self::$logs[] = array(
                'date'      => date('Y-m-d'),
                'time'      => date('H:i:s'),
                'type'      => strtoupper($type),
                'message'   => $message,
                'file' => (!empty($file) ? $file : $backtrace['file']),
                'line' => (!empty($line) ? $line : $backtrace['line']),
            );
            
        }
        
        /**
         * Returns last error informations
         * or false on logs empty
        **/
        public static function getLastError() {
            return (($total = count(self::$logs)) ? self::$logs[$total-1] : false);
        }
        
        
        /**
         * Register logs in a file
        **/
        public static function register() {
                        
            if(($error = error_get_last()) && ($total = count(self::$logs)) && $error != self::$logs[$total-1])
                self::parseError($error['type'], $error['message'], $error['file'], $error['line']);
                        
            if(empty(self::$logs)) return;
            
			
			mkpath($path = root(PATH_LOGS));
            $file = fopen($path . date('/Y-m-d').'.log', 'a+');
            
            $errors = array(); 
            foreach(self::$logs as $i => $log) {
                                
                $row = self::$format . PHP_EOL;
                foreach($log as $param => $value) {                            
                    $row = str_replace(":$param", $value, $row);
                }
                    
                if($log['type'] == self::LOG_ERROR)
                    $errors[] = $row;
                    
                fwrite($file, $row);
                    
            }
            
            fclose($file);
            
            // Send errors
            if(!empty($errors) && !empty($_SERVER['SERVER_ADMIN'])):
                    
                try {
                        
                    $mail = new Mail();
                    $mail->setObject('['.SYSTEM_DOMAIN.'] Fatal error(s)');
                    $mail->setFrom($_SERVER['SERVER_ADMIN'], 'Bug tracker');
                    $mail->addTo($_SERVER['SERVER_ADMIN']);
                    $mail->setBody(implode(PHP_EOL, $errors), Mail::FORMAT_TEXT);
                    $mail->send();
                        
                } catch(Exception $e) {
                        
                    Console::log('('.$e->getCode().') '. $e->getMessage(), self::LOG_ERROR);
                            
                }
            
            endif;
                                    
            return self::getLastError();
        }        
        
    }
    
?>