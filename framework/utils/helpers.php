<?php

    /**
     * This file contains all the helpers functions.
     * Beware, some of them use some configuration constants
     *
     * @package Core/Helpers
    **/

    /**
     * Generate an absolute URL with local project configuration
     *
     * @param   string      $path 		The path to extend
     * @param   string      $domain		The base domain
     * @param   string      $protocol	The protocol to use
     * @param   string      $port		The additional port
     * @return  string		The absolute URL
	 * @note	This function could be used to define an external URL. However is not recommended.
    **/
	function path($path = null, $domain = SYSTEM_DOMAIN, $protocol = SYSTEM_PROTOCOL, $port = null) {

        $domain = str_replace('~', SYSTEM_DOMAIN, $domain); // Default domain usage
        if(!empty($port)) $port = ":$port"; // Adding port if defined

        if(($length = strlen(SYSTEM_DIRECTORY)) != 0 && substr($path, 0, $length) == SYSTEM_DIRECTORY)
            $path = substr($path, $length);

        return "$protocol://$domain$port".SYSTEM_DIRECTORY.$path;
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
		return SYSTEM_ROOT.$path;

	}


    /**
     * Get the user accepted languages
     *
     * @param 	array     $reference	Codes of languages that would be appreciated
     * @return 	array					Returns an array of locales codes
    **/
    function get_accepted_languages(array $reference = array()) {
        if(isset($_SERVER) && !empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ))
            $accepted = explode(',', str_replace('-', '_', getenv('HTTP_ACCEPT_LANGUAGE') ));
        else
            $accepted = explode(' ', SYSTEM_LANGUAGES);

        if(!empty($reference))
            $languages = array_intersect($accepted, $reference);

        else
            $languages = $accepted;

        return $languages;
    }


    /**
     * Generate an XML string from an array
     *
     * @param   array     $array		Array to convert
     * @param   mixed     $xml			XML document root tag
     * @return  string					Returns a converted XML document as a string
    **/
    function xml_encode($array, $xml = 'document'){
        if(!is_object($xml))
            $xml = new SimpleXMLElement("<$xml/>");

        foreach($array as $key => $value) {
            if(is_array($value)):
                xml_encode($value, $xml->addChild($key));
            else:
                $xml->addChild($key, $value);
            endif;
        }

        return $xml->asXML();
    }



    /**
     * Order a two-dimensional array by a key
     *
     * @param   array       $array			Array to order
     * @param   string      $index			Key to use for ordering
     * @param   boolean     $ascending		Wether ascending order or not
     * @return  array						Returns the ordered array
	 * @note	In non respect of the ordering key in every sub array, an error will be generated by PHP
    **/
    function array_ksort($array, $index, $ascending = false) {
        $ordered = array();
        foreach($array as $key => $item){
            $ordered[$item[$index].$key] = $item;
        }
        ksort($ordered);

        $array = array();
        foreach($ordered as $item){
            $array[]= $item;
        }

        if($ascending)
            $array = array_reverse($array);

        return $array;
    }


    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array   	$array			Array to flat
     * @param  string  	$prepend		Items prefix
     * @return array					Returns a flatten array
    **/
    function array_dot($array, $prepend = '') {
        $output = array();

        foreach ($array as $key => $value) {

            if(is_array($value))
                $output = array_merge($output, array_dot($value, $prepend.$key.'.'));

            else
                $output[$prepend.$key] = $value;

         }

         return $output;
    }


    /**
     * Set a multi-dimensional associative array value from a string
     *
     * @param   string  	$path		Array's keys path
     * @param   mixed   	$value		Value to associate to the final key
     * @param   array   	$array		Source array that will contains the value
     * @return  array					Returns the alterated array
    **/
    function array_set($path, $value, &$array = array()) {
        $segments = explode('.', $path);

        foreach($segments as $index){
            $array[$index] = null;
            $array = &$array[$index];
        }

        $array = $value;
        return $array;
    }


    /**
     * Get a multi-dimensional associative array value from a string
     *
     * @param   string  	$path		Array's keys path
     * @param   array   	$array		Array that contains the value
     * @return  mixed   				Returns an associated key's value
    **/
    function array_value($path, $array, $default = null) {
        if(!empty($path)) {

            $keys = explode('.', $path);
            foreach ($keys as $key) {

                if (isset($array[$key]))
                    $array = $array[$key];
                else
                    return $default;

            }

        }

        return $array;
    }

    /**
     * Delete a multi-dimensional associative array index
     *
     * @param   string  	$path		Array's keys path
     * @param   array   	$array		Array that contains the key
     * @return  boolean   				Returns wether the key existed (and have been removed) or not
    **/
    function array_unset($path, &$array) {
        $segments = explode('.', $path);
        $last = count($segments)-1;
        foreach($segments as $i => $index){
            if(!isset($array[$index]))
                return false;

            if($i < $last)
                $array = &$array[$index];
        }

        if(!isset($array[$segments[$last]]))
            return false;

        unset($array[$segments[$last]]);
        return true;
    }


    /**
     * Get a file MIME type
	 *
	 * @note This function will try multiple way to get the information until found one (compatibility).
     * @param 	string 		$file		Path to the file to analyze
	 * @return	mixed					Returns the estimated file MIME type. However, false will returned if no method worked
    **/
    function get_mime_type($file) {
		if(function_exists('finfo_open')):
			$const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
			$finfo = finfo_open($const);
				return finfo_file($finfo, $file);
			finfo_close($finfo);

		elseif(function_exists('mime_content_type')):
			return mime_content_type($file);

		elseif(function_exists('exec')):
			$mime = trim(exec('file -b --mime-type '.escapeshellarg($file)));
			if (!$mime)
		    	$mime = trim(exec('file --mime '.escapeshellarg($file)));
		    if (!$mime)
		    	$mime = trim(exec('file -bi '.escapeshellarg($file)));

		    return $mime;
		endif;

		return false;
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
     * Create directory's path recursively if it not exists
     *
     * @param 	string     	$path		Path to create
	 * @param 	integer		$mode		Mode to set to folders
	 * @return	boolean		Return wether the path is available or not
	 * @note This function could returns false in case of in the lack of writing permission (errors generation disabled)
    **/
	function mkpath($path, $mode = 0755) {
		return is_dir($path) || @mkdir($path, $mode, true);
	}

    /**
     * Remove all directory's content and itself.
     *
     * @param    	string      $path       The directory's path
	 * @return 		boolean		Return wether the folder have been removed or not
	 * @note 	Could returns false in case of in the lack of writing permission (errors generation disabled).
	 *			The process is stopped at the first error.
    **/
    function rmpath($dir) {

        $data = scandir($path = $dir);

        foreach($data as $file) {
            if(!in_array($file, array('.', '..'))) {

                if(is_file($path.'/'.$file) || is_link($path.'/'.$file)) {
                    if(!@unlink($path.'/'.$file)) return false;
                }

                elseif(is_dir($path.'/'.$file)) {
                    if(!rmpath($dir.'/'.$file)) return false;

                }

            }
        }

        return @rmdir($path);

    }


    /**
     * Encode a path
     * @param 	string    $path 	Path to convert
	 * @return	string				Returns the encoded path
	 * @note Instead of the native url_encode function, accented letters are converted to non accented ones
    **/
    function path_encode($string) {

        $string = str_replace(array( // Characters replacements
            'á','à','â','ã','ª','ä','å','Á','À','Â','Ã','Ä','é','è','ê','ë','É','È','Ê','Ë',
            'í','ì','î','ï','Í','Ì','Î','Ï','œ','ò','ó','ô','õ','º','ø','Ø','Ó','Ò','Ô','Õ',
            'ú','ù','û','Ú','Ù','Û','ç','Ç','Ñ','ñ'
        ),array(
            'a','a','a','a','a','a','a','A','A','A','A','A','e','e','e','e','E','E','E','E',
            'i','i','i','i','I','I','I','I','oe','o','o','o','o','o','o','O','O','O','O','O',
            'u','u','u','U','U','U','c','C','N','n'
        ), $string);

        // Special characters replaced with a dash
        $string = str_replace(array(' ', '/', '+'), '-', $string);

        // Remove not authorized characters
        $string = preg_replace('#[^a-z0-9_-]#i', '', $string);

        return urlencode($string);
    }


    /**
     * Prevent magic quotes
	 *
     * @note Magic quotes deprecated as of PHP 5.3 and removed as of PHP 5.4)
     * @param 	mixed 	$input		Information in which magic quotes have to be stripped
     * @return 	mixed	Returns the quotes stripped information
    **/
    function strip_magic_quotes($input) {
		if(get_magic_quotes_gpc()):
			if(is_array($input)):

				foreach($input as $k => $v)
					$input[$k] = stripslashes($v);

			else:

				$input = stripslashes($input);

			endif;
		endif;

		return $input;
	}


    /**
     * Check if the variable is a closure function
     * @param 	mixed     $variable		Variable to check
     * @return 	boolean					Returns wether the variable is a closure function or not
    **/
    function is_closure(&$variable) {
        return (is_object($variable) && ($variable instanceof Closure));
    }

    /**
     * Check if it is a function either a closure
	 * @param 	mixed		$variable		Variable to check
	 * @return 	boolean						Returns wether the variable is a closure or a function name either none of these.
    **/
    function is_function(&$variable) {
        return function_exists($variable) || is_closure($variable);
    }

?>
