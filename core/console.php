<?php

    class Console {
        private static $session;
        private static $request;
        private static $headers;
        
        private static $logs        = array();
        private static $format      = '[:time] [:type] :log';
        private static $emails      = array();
        
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
        public function __construct($emails = array()) {
            self::$emails = $emails; 
        }
        
        /**
         * Add a log
        **/
        public static function log($log, $type = self::LOG_DEFAULT, $exit = false) {
            self::$logs[] = array(
                'date'      => date('Y-m-d'),
                'time'      => date('H:i:s'),
                'type'      => strtoupper($type),
                'log'       => $log,
                'fatal'     => $exit
            );
            
            if($exit):
                Console::register();
                Response::type('text', 503);
                Response::view('503');
                exit();
            endif;
        }
        
        
        /**
         * Add a debug log
        **/
        public static function debug($log, $exit = false) {
            self::log($log, Console::LOG_DEBUG, $exit);
        }
        
        
        /**
         * Add a notice log
        **/
        public static function notice($log) {
            self::log($log, Console::LOG_NOTICE);
        }
        
        
        /**
         * Add a warning log
        **/
        public static function warning($log) {
            self::log($log, Console::LOG_WARNING);
        }
        
        
        /**
         * Add a deprecated log
        **/
        public static function deprecated($log) {
            self::log($log, Console::LOG_DEPRECATED);
        }
        
        
        /**
         * Add an error log
        **/
        public static function error($log) {
           self::log($log, Console::LOG_ERROR); 
        }
        
        /**
         * Add a fatal error log
        **/
        public static function fatal($log) {
            self::log($log, Console::LOG_ERROR, true);
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
                                
                    $row = self::$format . "\r\n";
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
            if(!empty($fatals) && !empty(self::$emails)):
                $from = 'debug@'.SERVER_DOMAIN;
                $mail = new mail('['.SERVER_DOMAIN.'] Fatal error (log)');
                $mail->from('debug@'.SERVER_DOMAIN, 'Debug', 'Automatic email. Do not respond.');
                $mail->to(self::$emails[0]);
                $mail->Cc(self::$emails);
                $mail->content($fatals);
                $mail->send();
            endif;
                
            endif;
        }
        
    }
    
?>