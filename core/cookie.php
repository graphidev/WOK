<?php

    class Cookie {
        
        /**
         * Define a cookie        
        **/
        public static function set($name, $value, $duration = COOKIES_LIFETIME, $secured = false, $public = true) {
            if($secured)
                $value = self::_encrypt($value);
                                    
            if(empty($duration))
                $duration = COOKIES_LIFETIME;
            
            $directory = SYSTEM_DIRECTORY != '' ? SYSTEM_DIRECTORY : '/';
            $https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            
            $alias = explode(' ', SYSTEM_DOMAIN_ALIAS);
            foreach($alias as $i => $domain) {
                setcookie($name, $value, time()+$duration, $directory, $domain, $https, !$public);
            }
            
            return setcookie($name, $value, time()+$duration, $directory, SYSTEM_DOMAIN, $https, !$public);
        }
                
        /**
         * Get cookie existance
        **/
        public static function exists($name, $strict = false) {
            if($strict && !empty($_COOKIE[$name])):
                return true;
            else:
                return isset($_COOKIE[$name]);
            endif;
        }
        
        /**
         * Get cookie value
        **/
        public static function get($name, $decrypt = false) {
            if(self::exists($name, true))
                return  $decrypt ? self::_decrypt($_COOKIE[$name]) : $_COOKIE[$name];
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
            $module = mcrypt_module_open(COOKIES_CRYPT_ALGORITHM, '', COOKIES_CRYPT_MODE, '');
            mcrypt_generic_init($module, COOKIES_SALT, COOKIES_CRYPT_IV);
                
            $encrypted = mcrypt_generic($module, $value);
            
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
            
            return base64_encode($encrypted);
        }
        
        /**
         * Decrypt a cookie value
        **/
        private static function _decrypt($value) {
            $value = base64_decode($value);
            
            $module = mcrypt_module_open(COOKIES_CRYPT_ALGORITHM, '', COOKIES_CRYPT_MODE, '');
            mcrypt_generic_init($module, COOKIES_SALT, COOKIES_CRYPT_IV);
            
            $decrypted   = mdecrypt_generic($module, $value);
            
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
            
            return $decrypted;
        }
        
    }

?>