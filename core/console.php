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
                /*
                if(isset(self::$handlers[$type])) 
                    call_user_func(self::$handlers[$type], array($type, $message, $file, $line));
                
                elseif(isset(self::$handlers['error']))
                    call_user_func(self::$handlers['error'], array($type, $message, $file, $line));
                */
                
                self::log($message, $type, $file, $line);

                return !SYSTEM_DEBUG; // Show/hide error message
            });
            
            // Handle not catched exceptions
            set_exception_handler(function($e) {  
                if(SYSTEM_DEBUG)
                    echo $e->getMessage().' (Exception not catched) in '.$e->getFile().' on line '.$e->getLine();

                self::log($e->getMessage().' (Exception not catched)', self::LOG_ERROR, $e->getFile(), $e->getLine());
                /*
                if(isset(self::$handlers['exception'])) //  is_a ( object $object , string $class_name [, bool $allow_string = FALSE ] )
                    call_user_func(self::$handlers['exception'], array($e));
                */
            });
        }
        
        public static function handler($level, Closure $callback) {
            self::$handler['level'] = $callback;   
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
            
            
            if($type == self::LOG_ERROR && !SYSTEM_DEBUG):
                Response::view('503', 503)->render();
                exit;
            endif;
        }
        
        /**
         * Register logs in a file
        **/
        public static function register() {
            if(empty(self::$logs)) return;
                
            $file = fopen(root(PATH_LOGS . date('/Y-m-d').'.log'), 'a+');
            
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
            if(!empty($errors)):
                    
                try {
                        
                    $mail = new Mail();
                    $mail->object('['.SYSTEM_DOMAIN.'] Fatal error(s)');
                    $mail->from($_SERVER['SERVER_ADMIN'], 'Bug tracker');
                    $mail->to($_SERVER['SERVER_ADMIN']);
                    $mail->content(implode(PHP_EOL, $errors), Mail::FORMAT_TEXT);
                    $mail->send();
                        
                } catch(Exception $e) {
                        
                    Console::log('('.$e->getCode().') '. $e->getMessage(), self::LOG_ERROR);
                            
                }
            
            endif;
        }
        
        
    }
    
?>