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
     * The Headers class provide an interface
     * for both HTTP request and response headers
    **/
    class Headers implements \Iterator {


        /**
         * @var array       $headers      Available interface headers
        **/
        protected $headers = array();

        /**
         * @var integer     $position      Iterator position
        **/
        protected $position = 0;


        /**
         * Instanciante headers interface
         * @param array     $headers        Available headers
        **/
        public function __construct(array $headers = array()) {

            $this->headers  = array_change_key_case($headers, CASE_LOWER);
            $this->position = 0;

        }


        /**
         * Check a header availability
         * @param string    $name       Header name
        **/
        public function hasHeader($name) {

            return isset($this->headers[mb_strtolower($name)]);

        }


        /**
         * Get a header value
         * @param string    $name           Header name
         * @param string    $default        Alternative default header value
        **/
        public function getHeader($name, $default = false) {

            if(!isset($this->headers[mb_strtolower($name)]))
                return $default;

            return $this->headers[mb_strtolower($name)];

        }

        /**
         * Get a multiple header values
         * @param string    $name           Header name
         * @param array     $default         Alternative default header values
        **/
        public function getHeaderValues($name, array $default = null) {
            $header = $this->getHeader($name, $default);

            if($header === $default)
                return $default;

            $values = explode(',', $header);

            return array_map('trim', $values);
        }

        /**
         * Get a multiple header decreasingly ordered values
         * @param string    $name           Header name
         * @param array     $default         Alternative default header values
        **/
        public function getHeaderOrderedValues($name, array $default = null) {

            $values = $this->getHeaderValues($name, $default);

            if(!is_array($values))
                return $values;

            $quantified = array();
            foreach($values as $index => $item) {

                $qvalue = 1;
                if(($qpos = mb_strpos($item, $prefix = ';q=')) !== false) {

                    $qvalue         = mb_substr($item, $qpos + mb_strlen($prefix));
                    $item           = mb_substr($item, 0, $qpos); // Remove quality string
                    $values[$index] = $item;

                }

                $quantified[$item] = $qvalue;

            }

            uasort($values, function($a, $b) use($quantified) {
                return ($quantified[$a] > $quantified[$b] ? -1 : 1);
            });

            // Return reindexed values
            return array_values($values);

        }


        /**
         * Add a header value
         * @param string          $name           Header name
         * @param string|array    $value          Header's additional value
        **/
        public function addHeader($name, $value) {

            if(!is_array($value)) {
                $value = explode(',', $value);
                $value = array_map('trim', $value);
            }

            $source = $this->getHeaderValues($name, array());
            $value = (!empty($source) ? array_merge($source, $value) : $value);

            $this->setHeader($name, $value);
        }

        /**
         * For header new value
         * @param string          $name           Header name
         * @param string|array    $value          Header's new value
         * @note This method erase the previous header value if it exists
        **/
        public function setHeader($name, $value) {

            if(is_array($value)) {
                $value = implode(', ', $value);
            }

            $this->headers[mb_strtolower($name)] = $value;

        }


        /**
         * Get the headers collection
        **/
        public function __toArray() {
            return $this->headers;
        }


        function rewind() {
            reset($this->headers);
            $this->position = key($this->headers);
        }

        function current() {
            return $this->headers[$this->position];
        }

        function key() {
            return $this->position;
        }

        function next() {
            next($this->headers);
            $this->position = key($this->headers);
        }

        function valid() {
            return isset($this->headers[$this->position]);
        }

    }
