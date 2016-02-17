<?php

    /**
    * Web Operational Kit
    * The neither huger nor micro humble framework
    *
    * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @license     BSD <license.txt>
    **/

    namespace Message\Components;


    /**
     * The Uri class provide an interface
     * for the request URI
    **/
    class Uri {

        protected $scheme;
        protected $user;
        protected $pass;
        protected $host;
        protected $port;
        protected $path;
        protected $parameters = array();


        /**
         * Instanciate the URI interface from  a string URL
         * @param   string      $url          URL to parse
        **/
        public function __construct($url) {

            $parts = parse_url($url);

            $this->scheme     = isset($parts['scheme']) ? $parts['scheme'] : '';
            $this->username   = isset($parts['user'])   ? $parts['user']   : '';
            $this->password   = isset($parts['pass'])   ? $parts['pass']   : '';
            $this->host       = isset($parts['host'])   ? $parts['host']   : '';
            $this->port       = isset($parts['port'])   ? $parts['port']   : null;
            $this->path       = isset($parts['path'])   ? $parts['path']   : '';
            mb_parse_str((isset($parts['query']) ? $parts['query'] : ''), $parameters);

        }


        /**
         * Get the URI scheme
        **/
        public function getScheme() {
            return $this->scheme;
        }

        /**
         * Get the URI user
        **/
        public function getUsername() {
            return $this->username;
        }

        /**
         * Get the URI user password
        **/
        public function getPassword() {
            return $this->password;
        }

        /**
         * Get the URI host
        **/
        public function getHost() {
            return $this->host;
        }

        /**
         * Get the URI port
        **/
        public function getPort() {
            return $this->port;
        }

        /**
         * Get the URI path
        **/
        public function getPath() {
            return $this->path;
        }


        /**
         * Check if an URI parameter is available
         * @param   string      $name           Parameters'name
        **/
        public function hasParameter($name) {
            return isset($this->parameters[$name]);
        }

        /**
         * Get an URI parameter's value
         * @param   string      $name           Parameters'name
         * @param   string      $default        Parameters default value
        **/
        public function getParameter($name, $default = false) {
            if(!$this->hasParameter($name))
                return $default;

            return $this->parameters[$name];
        }


        /**
         * Get the original URI
        **/
        public function __toString() {

            $uri = $this->scheme.'://';

            if(!empty($this->username) && empty($this->password))
                $uri .= $this->username.':'.$this->password.'@';

            $uri .= $this->host;
            $uri .= $this->path;

            if(!empty($this->parameters))
                $uri .= '?'.http_build_query($this->parameters);

            return $uri;

        }

    }
