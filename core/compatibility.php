<?php

    /**
     * This file contains all the compatiblities functions for PHP
    **/


    /**
     * Remove magic_quotes from a string or an array
    **/
	function strip_magic_quotes($str) {
		if(get_magic_quotes_gpc()):
			if(is_array($str)):
				foreach($str as $key => $value) {
					$str[$key] = stripslashes($value);
				}
			else:
				$str = stripslashes($str);
			endif;
		endif;
	
		return $str;
	}

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
     * compatible function for strstr($haystack, $needle, true)
    **/
    function strstr_before($haystack, $needle) {
	
		if(PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 3):
			return strstr($haystack, $needle, true);
		else:
			$after = strstr($haystack, $needle);
			return str_replace($after, '', $haystack);
		endif;
	
	}

?>