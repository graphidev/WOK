<?php
    
    /** 
     * Manage errors and not catched exceptions.
     * It will register them as logs files (one per day)
     *
     * @package Core
    **/

    class Console {
        /**
         * Logs array (saved on register)
        **/
        private static $logs        = array();
        
        /**
         * Console configuration
        **/
        const ERRORS_REPORTING      = E_ALL;
        const LOGS_FORMAT           = '[:time] [:type] :message in :file on line :line';
        
        
        /**
         * Logs types
        **/
        const LOG_DEFAULT       = 'DEFAULT';        // Default log (unknow type)
        const LOG_ERROR         = 'ERROR';          // Something have to be revised : 503 response
        const LOG_WARNING       = 'WARNING';        // Something should be revised
        const LOG_NOTICE        = 'NOTICE';         // See WARNING
        const LOG_DEPRECATED    = 'DEPRECATED';     // Something should not be used anymore
        
        
        /**
         * Redefine logs format
        **/
        public static function handle() {
            error_reporting(Console::ERRORS_REPORTING);
            
            set_error_handler('Console::handler');   
            set_exception_handler('Console::exception');
        }
        
        /**
         * Handle errors
         * @param integer   $type
         * @param string    $message
         * @param string    $file
         * @param integer   $line
         * @return boolean
        **/
        public static function handler($type, $message, $file, $line){
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
            
            Console::log($message, $type, $file, $line);
            
            return !SYSTEM_DEBUG; // Show/hide error message
        }
        
        
        /**
         * Register non catched exceptions errors
         * @param Exception $e
        **/
        public static function exception($e) {  
            if(SYSTEM_DEBUG)
                echo $e->getMessage().' (Exception not catched) in '.$e->getFile().' on line '.$e->getLine();
            
            self::log($e->getMessage().' (Exception not catched)', self::LOG_ERROR, $e->getFile(), $e->getLine());
            
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
                Response::view('503', 503);
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
                                
                $row = Console::LOGS_FORMAT . PHP_EOL;
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