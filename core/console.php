<?php

    class Console {
        private static $session;
        private static $request;
        private static $headers;
        
        const LOG_DEFAULT = 'LOG';
        const LOG_ERROR = 'ERROR';
        
        public function __construct() {
            
        }
        
        private function define($log, $type = self::LOG_DEFAULT) {
            
            if(!is_dir(root(PATH_LOGS.date('/Y/m/d'))))
                mkdir(root(PATH_LOGS.date('/Y/m/d')), 0755, true);
            
            if(defined('CONSOLE_LOGS_RECEIVER'))
                sleep(1);
                
        }
        
        public function log($value) {
            $this->define($value);
        }
        
        public function error($value, $exit = false) {
           $this->define($value, self::LOG_ERROR);
            if($exit)
                exit("ERROR::$value");
            
        }
        
        // error($value, $exit = false);
        // log($value);
        // Error::[]
        // Log[]::fr_FR, 127.0.0.1
        
    }
    
?>