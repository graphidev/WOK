<?php

    /**
     * UTF 8 compatible functions
     * Please check PHP documentation for usage
     *
     * @package Core/Helpers/UTF-8
    **/
	
	/**
	 * Set UTF-8 namespace
	**/
    namespace UTF8;

    /**
     * Convert a string to an array
	 *
	 * @see http://php.net/str_split
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
     * Reverse a string
	 *
	 * @see http://php.net/strrev
     * @param string    $string
     * @return string
    **/
    function strrev($string) {
        $characters = str_split($string);
        return implode('', array_reverse($characters));
    }

    /**
     * Make a string's first character uppercase
	 *
	 * @see http://php.net/ucfirst
     * @param string
     * @return string
    **/
    function ucfirst($string) {
        $letter = mb_strtoupper(mb_substr($string, 0, 1));
        return $letter.mb_substr($string, 1);   
    }

?>