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
    class Cookies {

        const CRYPT_MODE = MCRYPT_MODE_CBC;
        const CRYPT_ALGORITHM = MCRYPT_RIJNDAEL_256;

        protected $path;
        protected $domain;
        protected $salt;
        protected $secure = false;

        public function __construct($salt, $domain = null, $path = '/', $secure = false) {

            $this->path     = $path;
            $this->domain   = $domain;
            $this->salt     = $salt;
            $this->secure   = $secure;

        }

        /**
         * Define a cookie
         * @param string    $name
         * @param string    $value
         * @param integer   $duration
         * @param string    $crypt      Crypt key
         * @param domain    $domain
         * @return boolean
        **/
        public function set($name, $value, $duration = 0, $crypt = false, $path = null, $domain = null) {

            $expire = (!empty($duration) ? time()+$duration : 0);

            if($crypt) $value = $this->_encrypt($value, $expire);

            $path   = (!empty($path) ? $path : $this->path);
            $domain = (!empty($domain) ? $domain : $this->domain);

            return setcookie($name, $value, $expire, $path, $domain, $this->secure, $this->secure);

        }

        /**
         * Get cookie value
         * @param string    $name
         * @param boolean   $decrypt    Decrypt salt
         * @return mixed
        **/
        public function get($name, $decrypt = false) {
            if(!$this->exists($name))
                return false;

            return ($decrypt ? $this->_decrypt($_COOKIE[$name]) : $_COOKIE[$name]);
        }

        /**
         * Get cookie existance
         * @param string    $name
         * @param boolean   $strict
         * @return boolean
        **/
        public function exists($name, $strict = false) {
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
        public function delete($name) {
            return setcookie($name, '', 1);
        }

        /**
         * Destroy all existing cookies
        **/
        public function clean() {
            $cookies = array_keys($_COOKIE);
            for($i=0; $i < count($cookies); $i++)
                setcookie($cookies[$i], '',time()-1);
        }

        /**
         * Get all cookies
         * @return array
        **/
        public function all() {
            return $_COOKIE;
        }


        /**
         * Encrypt a cookie value
         * @param string    $value
         * @param integer   $expire
         * @return string
        **/
        private function _encrypt($value, $expire) {
            $module = mcrypt_module_open(self::CRYPT_ALGORITHM, '', self::CRYPT_MODE, '');
            $iv = $this->_iv($expire, $module);
            mcrypt_generic_init($module, $this->salt, $iv);

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
        private function _decrypt($value) {
            list($value, $expire) = explode('|', $value);

            $module = mcrypt_module_open(self::CRYPT_ALGORITHM, '', self::CRYPT_MODE, '');
            $iv = $this->_iv(base64_decode($expire), $module);
            mcrypt_generic_init($module, $this->salt, $iv);

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
        private function _iv($key, &$module) {
            $size = mcrypt_enc_get_iv_size($module);
            $iv = sha1($key);

            if (strlen($iv) > $size)
                $iv = substr($iv, 0, $size);

            return $iv;
        }

    }

?>
