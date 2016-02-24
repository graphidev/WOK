<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <licence.txt>
    **/

    /**
     * The helpers provide a unicode support.
     * @package Helpers
    **/

    if (!function_exists('mb_ucfirst')) {
        /**
         * Unicode ucfirst
         * @see http://php.net/ucfirst
        **/
        function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false) {

            $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
            $str_end = "";

            if ($lower_str_end) {
                $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
            }
            else {
                $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
            }

            $str = $first_letter . $str_end;
            return $str;

        }
    }


    if (!function_exists('mb_str_replace')) {

        /**
         * Unicode str_replace
         * @see http://php.net/str_replace
        **/
        function mb_str_replace($search, $replace, $subject, &$count = 0, $encoding = 'auto') {

            if(!is_array($subject)) {

                $searches = is_array($search) ? array_values($search) : [$search];
                $replacements = is_array($replace) ? array_values($replace) : [$replace];
                $replacements = array_pad($replacements, count($searches), '');

                foreach($searches as $key => $search) {

                    $replace = $replacements[$key];
                    $search_len = mb_strlen($search, $encoding);

                    $sb = [];
                    while(($offset = mb_strpos($subject, $search, 0, $encoding)) !== false) {
                        $sb[] = mb_substr($subject, 0, $offset, $encoding);
                        $subject = mb_substr($subject, $offset + $search_len, null, $encoding);
                        ++$count;
                    }
                    $sb[] = $subject;
                    $subject = implode($replace, $sb);

                }

            }
            else {

                foreach($subject as $key => $value) {
                    $subject[$key] = mb_str_replace($search, $replace, $value, $count, $encoding);
                }

            }

            return $subject;

        }

    }


    if(!function_exists('mb_str_split')) {

        /**
         * Unicode str_split
         * @see http://php.net/str_split
         * @source https://gist.github.com/girvan/2155412
        **/
        function mb_str_split($string, $string_length = 1) {

            if(mb_strlen($string) > $string_length || !$string_length) {

                do {
                        $c          = mb_strlen($string);
                        $parts[]    = mb_substr($string, 0, $string_length);
                        $string     = mb_substr($string, $string_length);
                } while(!empty($string));

            } else {
                $parts = array($string);
            }

            return $parts;

        }

    }
