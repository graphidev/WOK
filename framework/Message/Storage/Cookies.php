<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Message\Storage;

    /**
     * Manage request cookies.
    **/
    class Cookies /*implements \Iterator*/  {

        protected $path;
        protected $domain;
        protected $secure       = false;

        public function __construct($domain = null, $path = '/', $secure = false) {

            $this->path     = $path;
            $this->domain   = $domain;
            $this->secure   = $secure;

        }

        /**
         * Define a cookie
         * @param string    $name
         * @param string    $value
         * @param integer   $duration
         * @param domain    $domain
         * @return boolean
        **/
        public function set($name, $value, $duration = 0, $path = null, $domain = null) {

            $expire = (!empty($duration) ? time()+$duration : 0);

            $path   = (!empty($path) ? $path : $this->path);
            $domain = (!empty($domain) ? $domain : $this->domain);

            return setcookie($name, $value, $expire, $path, $domain, $this->secure, $this->secure);

        }

        /**
         * Get cookie value
         * @param string    $name
         * @return mixed
        **/
        public function get($name) {
            if(!$this->exists($name))
                return false;

            return $_COOKIE[$name];
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

    }
