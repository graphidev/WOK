<?php

    /**
     * UTF 8 compatible functions
     * Please check PHP documentation for usage
     *
     * @package Helpers
    **/
    namespace UTF8;

    /**
     * str_split 
     * @param string    $string
     * @param integer   $split_length
     * @return array
    **/
    function str_split($string, $split_length = 0) {
        if ($split_length > 0) {
            $characters = array();
            $length = mb_strlen($string, "UTF-8");
            for ($i = 0; $i < $len; $i += $split_length) {
                $characters[] = mb_substr($string, $i, $split_length, "UTF-8");
            }
            return $charaters;
        }
        return preg_split("//u", $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * strrev
     * @param string    $string
     * @return string
    **/
    function strrev($string) {
        $characters = str_split($string);
        return implode('', array_reverse($characters));
    }

    /**
     * ucfirst
     * @param string
     * @return string
    **/
    function ucfirst($string) {
        $letter = mb_strtoupper(mb_substr($string, 0, 1));
        return $letter.mb_substr($string, 1);   
    }

?>