<?php

    /**
     * This file contains all the compatiblities functions for PHP
    **/
    
    namespace Compatibility;

    /**
     * Return the MIME type of a file
    **/
	function get_mime_type($filepath) {
		if(function_exists('finfo_open')):
			$const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
			$finfo = finfo_open($const);
				return finfo_file($finfo, $filepath);
			finfo_close($finfo);
    
		elseif(function_exists('mime_content_type')):
			return mime_content_type($filepath);
			
		elseif(function_exists('exec')):
			$mime = trim(exec('file -b --mime-type '.escapeshellarg($filepath)));
			if (!$mime)
		    	$mime = trim(exec('file --mime '.escapeshellarg($filepath)));
		    if (!$mime)
		    	$mime = trim(exec('file -bi '.escapeshellarg($filepath)));
		    
		    return $mime;
		endif;
	}
    

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
?>