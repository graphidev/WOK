<?php

    /**
     * This file contains all the compatiblities functions for PHP
    **/
    
    namespace Compatibility;

    /**
     * The unicode compatible function of str_split
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
     * The unicode compatible function of strrev
    **/
    function strrev($string) {
        $characters = str_split($string);
        return implode('', array_reverse($characters));
    }

    /**
     * The unicode comptabile function of ucfirst
    **/
    function ucfirst($string) {
        $letter = mb_strtoupper(mb_substr($string, 0, 1));
        return $letter.mb_substr($string, 1);   
    }

?>