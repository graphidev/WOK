<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Framework\Runtime;

    /**
     * The Request class contains the representation of
     * an HTTP request entrypoint, storing URI,
     * super globals data and parameters informations.
     * @note Some informations may not be override for security reasons
    **/
    class Request {

        /**
         * @var object  Class static instance
        **/
        private static $instance;

        /**
         * @var string  String representation of the request protocol
        **/
        protected $protocol;

        /**
         * @var string  Root request base
        **/
        protected $base;

        /**
         * @var string  Absolute request URI (without project base)
        **/
        protected $query;

        /**
         * @var string  Request URI (without parameters)
        **/
        protected $path;

        /**
         * @var string  Request domain
        **/
        protected $domain;

        /**
         * @var integer  Request port
        **/
        protected $port;

        /**
         * @var string  Request method (GET, POST, PUT, DELETE)
        **/
        protected $method;

        /**
         * @var array  Request headers (without parameters)
        **/
        protected $headers;

        /**
         * @var array  Request parameters (using $_GET)
        **/
        private $parameters = array();

        /**
         * @var array  Input files (using $_FILES)
        **/
        protected $files;

        /**
         * @var array  Input data (using $_FILES)
        **/
        protected $data = array();

        /**
         * @var integer Data range
        **/
        protected $range;

        /**
         * @var stream  PUT request content
        **/
        protected $content;

        /**
         * @var string  Requested file format (extension)
         * @deprecated
        **/
        protected $format;


        /**
         * Build the request object using server superglobals
         * either custom informations.
         * @param array   $informations   Custom request informations
        **/
        public function __construct() {

            // Calculate application base
            $root = realpath($_SERVER["DOCUMENT_ROOT"]);
            $this->base = substr(SYSTEM_ROOT, strlen($root));

            // Register informations
            $this->query      = (string) (!empty($this->base) ? substr($_SERVER['REQUEST_URI'], strlen($this->base)) : $_SERVER['REQUEST_URI']);
            $this->path       = (strpos($this->query, '?') ? strstr($this->query, '?', true) : $this->query);
            $this->format     = pathinfo($this->path, PATHINFO_EXTENSION);
            $this->domain     = $_SERVER['HTTP_HOST'];
            $this->protocol   = strtolower(strstr($_SERVER['SERVER_PROTOCOL'], '/', true) . ($this->secure() ? 'S' : ''));
            $this->port       = $_SERVER['SERVER_PORT'];
            $this->method     = mb_strtoupper($_SERVER['REQUEST_METHOD']);
            $this->headers	  = getallheaders();
            $this->parameters = $_GET;
            $this->data       = $_POST;
            $this->files      = $_FILES;
            $this->range      = (isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : false);

            // Force parameters definition
            if(empty($this->parameters) && ($parameters = substr($this->query, strlen($this->query)+1))):

                foreach(explode('&', $parameters) as $i => $parameter) {
                    @list($name, $value) = explode('=', $parameter);
                    $this->parameters[$name] = (isset($value) ? $value : true);
                }

            endif;


            self::$instance = $this;

        }


        /**
         * Get a define request informations
         * @param string    $property       Requested information's name
         * @return mixed    Returns the request information
         * @note Generate an user error on bad requested property
        **/
        public function __get($property) {
            if(!isset($this->$property))
                trigger_error('Undefined Request::'.$property, E_USER_ERROR);

            return $this->$property;
        }

        /**
         * Check wether the method matches or not
         * @param string      $name       Method to validate
         * @return boolean    Returns wether the method matches or not
        **/
        public function method($name) {
            return (mb_strtoupper($name) == $this->method);
        }

        /**
         * Get a request parameter
         * @param string    $name       Requested parameter's name
         * @return mixed    Returns the parameter's value or false
        **/
        public function parameter($name) {

            if(!isset($this->parameters[$name]))
                return false;

            return $this->parameters[$name];

        }

        /**
         * Get an input data
         * @param string    $name       Input data's name
         * @return mixed    Returns the data's value or false
        **/
        public function data($name) {

            if(!isset($this->data[$name]))
                return false;

            return $this->data[$name];

        }


        /**
         * Get an input file
         * @param string    $name       Input file's name
         * @return mixed    Returns the file or false
        **/
        public function file($name) {

            if(!isset($this->files[$name]) || !is_uploaded_file($this->files[$name]))
                return false;

            return $this->files[$name];

        }


        /**
         * Check wether connexion is secured or not
         * @return boolean  Returns true on secured connexion, false otherwise
        **/
        public static function secure() {
            return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
        }


        /**
         * Check wether the request is an XML HTTP Request or not
         * @return boolean  Returns true on XML HTTP Request, false otherwise
        **/
        public static function ajax() {
            $xhr = getenv('HTTP_X_REQUESTED_WITH');
            return (!empty($xhr) && mb_strtolower($xhr) == 'xmlhttprequest');
        }

        /**
         * Get the string formatted request
        **/
        public function __toString() {
            return strtoupper($this->protocol).' '.$this->path.' '.$this->port;
        }

    }
