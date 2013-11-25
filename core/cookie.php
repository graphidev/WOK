<?php

    class Cookie {
        
        /**
         * Define a cookie        
        **/
        public static function set($name, $value, $duration = null, $secured = false, $public = true) {
            if($secured)
                $value = self::_encrypt($value);
            
            setcookie($name, $value, time()+MAX_COOKIES_LIFETIME, '/', '.', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'), !$public);
        }
                
        /**
         * Get cookie existance
        **/
        public static function exists($name, $strict = false) {
            if($strict && !empty($_COOKIE[$name])):
                return true;
            else:
                return (isset($_COOKIE[$name]));
            endif;
        }
        
        /**
         * Get cookie value
        **/
        public static function get($name, $decrypt = false) {
            if(self::exists($name, true))
                return  $decrypt ? self::_decrypt($_COOKIE['name']) : $_COOKIE['name'];
            else
                return false;
        }
        
        /**
         * Delete a cookie
        **/
        public static function destroy($name) {
            setcookie($name, null, time(), '/', '.', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'), true);
        }
        
        
        /**
         * Encrypt a cookie value
        **/
        private static function _encrypt($value) {
            return strrev($value);
            //return $value;
        }
        
        /**
         * Decrypt a cookie value
        **/
        private static function _decrypt($value) {
            return strrev($value);
        }
        
    }

?>