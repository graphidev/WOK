<?php

    class Cookie {
        
        /**
         * Define a cookie        
        **/
        public static function set($name, $value, $duration = COOKIES_LIFETIME, $secured = false) {
                                    
            if(empty($duration))
                $duration = COOKIES_LIFETIME;
            
            $expire = time()+$duration;
            
            if($secured)
                $value = self::_encrypt($value, $expire);
            
            $directory = SYSTEM_DIRECTORY != '' ? SYSTEM_DIRECTORY : '/';
            $https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            
            $alias = explode(' ', SYSTEM_DOMAIN_ALIAS);
            foreach($alias as $i => $domain) {
                setcookie($name, $value, $expire, $directory, $domain, $https, $secured);
            }
            
            return setcookie($name, $value, $expire, $directory, SYSTEM_DOMAIN, $https, $secured);
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
            $https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            $directory = SYSTEM_DIRECTORY != '' ? SYSTEM_DIRECTORY : '/'; 
                        
            $alias = explode(' ', SYSTEM_DOMAIN_ALIAS);
            foreach($alias as $i => $domain) {
                setcookie($name, '', time(), $directory, $domain, $https);
            }
            
            return setcookie($name, '', time(), $directory, SYSTEM_DOMAIN, $https);
        }
        
        
        /**
         * Encrypt a cookie value
        **/
        private static function _encrypt($value, $expire) {
            $module = mcrypt_module_open(COOKIES_CRYPT_ALGORITHM, '', COOKIES_CRYPT_MODE, '');
            $iv = self::_iv($expire, $module);
            mcrypt_generic_init($module, COOKIES_SALT, $iv);
                
            $encrypted = mcrypt_generic($module, $value);
            
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
            
            return base64_encode($encrypted).'|'.base64_encode($expire);
        }
        
        /**
         * Decrypt a cookie value
        **/
        private static function _decrypt($value) {
            list($value, $expire) = explode('|', $value);
            
            $module = mcrypt_module_open(COOKIES_CRYPT_ALGORITHM, '', COOKIES_CRYPT_MODE, '');
            $iv = self::_iv(base64_decode($expire), $module);
            mcrypt_generic_init($module, COOKIES_SALT, $iv);
            
            $decrypted   = mdecrypt_generic($module, base64_decode($value));
            
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
            
            return $decrypted;
        }
        
        /**
         * Generate an IV to crypt and decrypt
        **/
        private static function _iv($key, &$module) {
            $size = mcrypt_enc_get_iv_size($module);
            $iv = md5($key);
            
            if (strlen($iv) > $size)
                $iv = substr($iv, 0, $size);
            
            return $iv;
        }
        
    }

?>