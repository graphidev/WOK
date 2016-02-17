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
     * This file contains all the strings helpers functions.
     * Beware, some of them use some configuration constants
     * @package Core/Helpers/Strings
    **/


    /**
	 * Replace the directory separator with the specific OS directory separator
	 *
	 * @param 	string 		$path		Path to translate
	 * @return 	string 		Returns an OS fixed file path
	**/
	function ospath($path) {

		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);

	}

	/**
     * Prefix path with the project root's path
     *
     * @param   string  $path		The path to extend
     * @return  string				Returns an absolute root path
	 * @note This function does not expand symbolic links as realpath()
    **/
	function root($path = null) {

        // Windows' server compatibility
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		return APPLICATION_ROOT.$path;

	}


    /**
     * Get the number of seconds from a string
     *
     * @param   string     $string		String to convert in seconds
     * @return  integer					Returns the number of seconds represented by the string
    **/
    function transtime($string, $time = null) {

        if(empty($time))
            $time = time();

        return strtotime($string, $time) - $time;
        
    }



    /**
     * Convert html characters to entities in either array or string data.
     * This function is a security one against XSS breach.
     *
     * @param   string|array      $data         Data to convert
     * @param   integer           $flags        Convertion flags. See native htmlentities function documentation
     * @param   boolean           $substrings   Force convertion all characters (not only html ones)
     * @param   boolean           $force        Force convertion of still converted entities
     * @return  string|array                    Converted data
    **/
    function entitieschars($data, $flags = ENT_COMPAT, $substrings = false,  $force = true) {
        if(is_array($data) || $data instanceof Traversable) {
            foreach($data as $item => $value) {
                $data[$item] =  entitieschars($value);
            }
        }

        elseif(is_string($data)) {
            $data = $substrings ? htmlentities($data, $flags, 'UTF-8', $force) : htmlspecialchars($data, $flags, 'UTF-8', $force);
        }

        return $data;
    }

    /**
     * Encode a path
     * @param 	string    $path 	Path to convert
	 * @return	string				Returns the encoded path
	 * @note Instead of the native url_encode function, accented letters are converted to non accented ones
    **/
    function path_encode($string, $strtolower = false) {

        $string = str_replace(array( // Characters replacements
            'á','à','â','ã','ª','ä','å','Á','À','Â','Ã','Ä','é','è','ê','ë','É','È','Ê','Ë',
            'í','ì','î','ï','Í','Ì','Î','Ï','œ','ò','ó','ô','õ','º','ø','Ø','Ó','Ò','Ô','Õ',
            'ú','ù','û','Ú','Ù','Û','ç','Ç','Ñ','ñ'
        ),array(
            'a','a','a','a','a','a','a','A','A','A','A','A','e','e','e','e','E','E','E','E',
            'i','i','i','i','I','I','I','I','oe','o','o','o','o','o','o','O','O','O','O','O',
            'u','u','u','U','U','U','c','C','N','n'
        ), $string);

		// Remove not authorized characters
        $string = preg_replace('#[^\sa-z0-9_-]#i', '', $string);

        // Special characters replaced with a dash
		$string = str_replace(array(' ', '/', '+', '-', '_'), '-', trim($string));

        // Transform to a lower case string
        if($strtolower)
            $string = mb_strtolower($string);

		// Encode URL for not transformed characters
		$string = urlencode($string);

		return $string;

    }
