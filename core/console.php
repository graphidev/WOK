<?php

    class Console {
        /**
         * Logs array (saved on register)
        **/
        private static $logs        = array();
        
        /**
         * Logs types
        **/
        const LOG_DEFAULT       = 'DEFAULT'; // Default log
        const LOG_ERROR         = 'ERROR'; // Error
        const LOG_WARNING       = 'WARNING'; // 
        const LOG_NOTICE        = 'NOTICE';
        const LOG_DEPRECATED    = 'DEPRECATED';
        const LOG_DEBUG         = 'DEBUG';
        
        
        /**
         * Redefine logs format
        **/
        public function __construct() {
            if(CONSOLE_HANDLER_LEVEL !== false):
                error_reporting(CONSOLE_HANDLER_LEVEL);
                set_error_handler('Console::handler');
            endif;
        }
        
        public static function handler($type, $message, $file, $line){
            if(!(error_reporting() & $type)) return;
            
            switch ($type) {
                case E_USER_ERROR:
                    Console::fatal("$message in $file : line $line");
                    break;
                            
                case E_USER_WARNING:
                    Console::warning("$message in $file : line $line");
                    break;
                    
                case E_USER_NOTICE:
                    Console::notice("$message in $file : line $line");
                    break;

                default:
                    Console::log("$message in $file : line $line", Console::LOG_ERROR);
            }
            
            return true;
        }
        
        /**
         * Add a log
        **/
        public static function log($message, $type = self::LOG_DEFAULT, $exit = false) {
            self::$logs[] = array(
                'date'      => date('Y-m-d'),
                'time'      => date('H:i:s'),
                'type'      => strtoupper($type),
                'message'   => $message,
                'fatal'     => $exit
            );
            
            if($exit):
                Console::register();
                $response = new Response;
                $response->view('503', 503);
                exit;
            endif;
        }
        
        
        /**
         * Add a debug log
        **/
        public static function debug($message, $exit = false) {
            self::log($message, Console::LOG_DEBUG, $exit);
        }
        
        
        /**
         * Add a notice log
        **/
        public static function notice($message) {
            self::log($message, Console::LOG_NOTICE);
        }
        
        /**
         * Add a warning log
        **/
        public static function warning($message) {
            self::log($message, Console::LOG_WARNING);
        }
        
        /**
         * Add a deprecated log
        **/
        public static function deprecated($message) {
            self::log($message, Console::LOG_DEPRECATED);
        }
        
        /**
         * Add an error log
        **/
        public static function error($message) {
           self::log($message, Console::LOG_ERROR); 
        }
        
        /**
         * Add a fatal error log
        **/
        public static function fatal($message) {
            self::log($message, Console::LOG_ERROR, true);
        }
        
        /**
         * Register logs in a file
        **/
        public static function register() {
            if(!empty(self::$logs)):
                
                $date = date('Y-m-d');
                $fatals = null;
            
                if(!is_dir(root(PATH_LOGS . "/$date")))
                    mkdir(root(PATH_LOGS . "/$date"), 0755, true);
                
                $types = array('default', 'deprecated', 'debug', 'error', 'fatal');
                
                foreach($types as $i => $type) {
                    $files[$type] = fopen(root(PATH_LOGS . "/$date/$type.log"), 'a+');    
                }                
                
                foreach(self::$logs as $i => $log) {
                                
                    $row = CONSOLE_LOG_FORMAT . "\r\n";
                    foreach($log as $param => $value) {                            
                        $row = str_replace(":$param", $value, $row);
                    }
                    
                    if($log['fatal']):
                        $filename = 'fatal';
                        $fatals .= $row;
                    
                    elseif(in_array(strtolower($log['type']), $types)):
                        $filename = strtolower($log['type']);
                    
                    else:
                        $filename = 'default';
                    endif;
                    
                    fwrite($files[$filename], $row);
                    
                }
            
                foreach($files as $name => $file) {
                    fclose($file);
                }
            
            /**
             * Send fatal errors by e-mail
            **/
            if(!empty($fatals) && defined(CONSOLE_FATAL_EMAILS) && CONSOLE_FATAL_EMAILS != null):
                $emails = explode(' ', CONSOLE_FATAL_EMAILS);
                $mail = new mail('['.SERVER_DOMAIN.'] Fatal error (log)');
                $mail->from('debug@'.SERVER_DOMAIN, 'Debug', 'Automatic email. Do not respond.');
                $mail->to($emails[0]);
                $mail->Cc($emails);
                $mail->content($fatals);
                $mail->send();
            endif;
                
            endif;
        }
        
    }
    
?>