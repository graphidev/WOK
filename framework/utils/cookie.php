<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Framework\Utils;

    /**
     * Manage request cookies.
     * Can also crypt and uncrypt them (security feature)
     *
     * Reserved cookies' names : language, uniqid
     *
    **/
    class Cookie {

        const CRYPT_MODE = MCRYPT_MODE_CBC;
        const CRYPT_ALGORITHM = MCRYPT_RIJNDAEL_256;


        /**
         * Define a cookie
         * @param string    $name
         * @param string    $value
         * @param integer   $duration
         * @param domain    $domain
         * @return boolean
        **/
        public static function set($name, $value, $duration = 0, $crypt = false, $domain = null) {
            $expire = (!empty($duration) ? time()+$duration : $duration);

            if($crypt) $value = self::_encrypt($value, $expire);

            $path = SYSTEM_DIRECTORY != '' ? SYSTEM_DIRECTORY : '/';

            return setcookie($name, $value, $expire, $path, $domain, Request::secure(), Request::secure());

        }

        /**
         * Get cookie value
         * @param string    $name
         * @param boolean   $decrypt
         * @return mixed
        **/
        public static function get($name, $decrypt = false) {
            if(self::exists($name))
                return ($decrypt ? self::_decrypt(strip_magic_quotes($_COOKIE[$name])) : strip_magic_quotes($_COOKIE[$name]));
            else
                return false;
        }

        /**
         * Get cookie existance
         * @param string    $name
         * @param boolean   $strict
         * @return boolean
        **/
        public static function exists($name, $strict = false) {
            if($strict && !empty($_COOKIE[$name]))
                return true;
            else
                return isset($_COOKIE[$name]);
        }

        /**
         * Delete a cookie
         * @param string    $name
         * @return boolean
        **/
        public static function destroy($name) {
            return setcookie($name, '', 1);
        }

        /**
         * Destroy all existing cookies
        **/
        public static function clean() {
            $cookies = array_keys(strip_magic_quotes($_COOKIE));
            for($i=0; $i < count($cookies); $i++)
                setcookie($cookies[$i], '',time()-1);
        }

        /**
         * Get all cookies
         * @return array
        **/
        public static function all() {
            return strip_magic_quotes($_COOKIE);
        }


        /**
         * Encrypt a cookie value
         * @param string    $value
         * @param integer   $expire
         * @return string
        **/
        private static function _encrypt($value, $expire) {
            $module = mcrypt_module_open(self::CRYPT_ALGORITHM, '', self::CRYPT_MODE, '');
            $iv = self::_iv($expire, $module);
            mcrypt_generic_init($module, COOKIES_SALT, $iv);

            $encrypted = mcrypt_generic($module, $value);

            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);

            return base64_encode($encrypted).'|'.base64_encode($expire);
        }

        /**
         * Decrypt a cookie value
         * @param string    $value
         * @return string
        **/
        private static function _decrypt($value) {
            list($value, $expire) = explode('|', $value);

            $module = mcrypt_module_open(self::CRYPT_ALGORITHM, '', self::CRYPT_MODE, '');
            $iv = self::_iv(base64_decode($expire), $module);
            mcrypt_generic_init($module, COOKIES_SALT, $iv);

            $decrypted   = mdecrypt_generic($module, base64_decode($value));

            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);

            return $decrypted;
        }

        /**
         * Generate an IV to crypt and decrypt
         * @param string    $key
         * @param resource
         * @return
        **/
        private static function _iv($key, &$module) {
            $size = mcrypt_enc_get_iv_size($module);
            $iv = sha1($key);

            if (strlen($iv) > $size)
                $iv = substr($iv, 0, $size);

            return $iv;
        }

    }

?>
