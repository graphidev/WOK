<?php

    /**
     * Web Operational Kit
     * The neither huge no micro extensible framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/


    /**
     * This file contains all the supports helpers functions.
     * These definitions provides functions that are not
     * enabled in the current PHP environment
     * @package Helpers
    **/


    if (!function_exists('getallheaders') && isset($_SERVER))  {

        /**
         * Fetch all HTTP request Headers
         * @see http://stackoverflow.com/questions/13224615/get-the-http-headers-from-current-request-in-php
         * @note This function is only available when called by a webserver
        **/
        function getallheaders() {

            if (!is_array($_SERVER)) {
                return array();
            }

            $headers = array();
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;

        }

    }


    if(!function_exists('iterator_to_array')) {

        /**
         * Copy the iterator into an array
         * @see http://php.net/manual/en/function.iterator-to-array.php
        **/
        function iterator_to_array(\Traversable $iterator, $use_keys = true) {

            $array = array();

            foreach($iterator as $key => $value) {
                if ($use_keys) {
                    $array[$key] = $value;
                } else {
                    $array[] = $value;
                }
            }

            return $array;

        }

    }
