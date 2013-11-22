<?php

    class Session {
        
        private static $IP;
        private static $browser;
        
        public static function start() {          
            
            /**
             * Define request IP
            **/
            if(!empty($_SERVER["HTTP_CLIENT_IP"])):
                self::$IP = $_SERVER["HTTP_CLIENT_IP"];
            elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])):
                self::$IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
            else:
                self::$IP = $_SERVER["REMOTE_ADDR"];
            endif;
            
            /**
             * Get browser informations
            **/
            self::$browser = @get_browser(null, true);
            
            
            /**
             * Define default session parameters
             * Only if they are not yet defined
            **/
            if(!isset($_SESSION[SESSION_SALT])):
                // $_SERVER['HTTP_ACCEPT_LANGUAGE']
            endif;   
        }
        
        public static function get($parameter) {
            if(isset($_SESSION[SESSION_SALT][$parameter]))
                return $_SESSION[SESSION_SALT][$parameter];
            else
                return false;
        }
        
        public static function set($parameter, $value) {
            $_SESSION[SESSION_SALT][$parameter] = $value;
        }
        
        public static function language($set = null) {
            if(!empty($set)):
                self::set('language', $set);
                setcookie('language', $set, time()+MAX_COOKIES_LIFETIME, '/', Request::get('secured'), false);
            else:
                $language = self::get('language');
                if(!$language) $language = @$_COOKIE['language'];
                
                return (!empty($language) ? $language : false);
            endif;
        }
        
    }

?>