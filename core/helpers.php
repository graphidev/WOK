<?php
    
    /**
     * This file contains all the helpers functions. 
     * Beware, some of them use some configuration constants
     *
     * @package Helpers
    **/

    
    /**
     * Get absolute URL path
     * 
     * @param   string      $path
     * @param   string      $domain
     * @param   string      $protocol
     * @param   string      $port
     * @return  string
    **/
	function path($path = null, $domain = SYSTEM_DOMAIN, $protocol = SYSTEM_PROTOCOL, $port = null) {
        
        $domain = str_replace('~', SYSTEM_DOMAIN, $domain); // Default domain usage
        if(!empty($port)) $port = ":$port"; // Adding port if defined
                
        if(($length = strlen(SYSTEM_DIRECTORY)) != 0 && substr($path, 0, $length) == SYSTEM_DIRECTORY)
            $path = substr($path, $length);
               
        return "$protocol://$domain$port".SYSTEM_DIRECTORY."$path";
	}
	

	/**
     * Get absolute server root path
     *
     * @param   string  $path
     * @return  string
    **/
	function root($path = null) {
        // Windows' server compatibility
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        
		return SYSTEM_ROOT.$path;
	}


    /**
     * Load library if available
    **/
    function load_library($name) {
        if(!file_exists($library = SYSTEM_ROOT.PATH_LIBRARIES."/$name.library.php")):        
            $e = new ExtendedInvalidArgumentException("Library $name not found");
            $e->setCallFromTrace();
            throw $e;
        endif;
            
        require_once $library;
    }

    
    /**
     * Get accepted languages
     *
     * @param array     $reference
     * @return array
    **/
    function get_accepted_languages(array $reference = array()) {
        $accepted  = explode(',', str_replace('-', '_', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
                
        if(!empty($reference))
            $languages = array_intersect($accepted, $reference);
        
        else
            $languages = $accepted;
                
        return $languages;        
    }


    
    /**
     * Shortcut to Locales::_e() method
     *
     * @param string    $path
     * @param array     $data
     * @return string
    **/
    function _e($path, $data = array()) {
        return Locales::_e($path, $data);
    }


    /**
     * Generate an XML string from an array
     *
     * @param   array     $array
     * @param   mixed     $xml
     * @return  string
    **/
    function xml_encode($array, $xml = 'document'){
        if(!is_object($xml))
            $xml = new SimpleXMLElement("<$xml/>");
                
        foreach($array as $key => $value) {
            if(is_array($value)):
                print_r($value);
                xml_encode($value, $xml->addChild($key));
            else:
                $xml->addChild($key, $value);
            endif;
        }

        return $xml->asXML();
    }
    


    /**
     * Order an array by an index
     *
     * @param   array       $array
     * @param   string      $index
     * @param   boolean     $ascending
     * @return  array
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
     * @param  array   $array
     * @param  string  $prepend
     * @return array
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
     * @param   string  $path
     * @param   mixed   $value
     * @param   array   $array
     * @return  array
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
     * @param   string  $path
     * @param   array   $array
     * @return  mixed   
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
     * @param   string  $path
     * @param   array   $array
     * @return  boolean   
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
     * Get file MIME type
     * @param string $file
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
     * Check directory's path
     * Create directory if not exists
     *
     * param string     $path
    **/
    function makedir($path, $mode = 0755) {
        return is_dir($path) || @mkdir($path, $mode, true); 
    }

    
    /**
     * Analyse a folder and return files and subfolders names
     * Please use this function carefuly : it is recursive
     * @param string    $path
     * @param array     $ignore
     * @return array
	**/
	function explore($dir, $ignore = array()) {
        if(!is_readable($dir)) return false;
        
        $ignore     = array_merge($ignore, array('.', '..', '.DS_Store', 'Thumbs.db'));
	    $handle     = opendir($dir);
	    $array      = array();
	    
	    while(false !== ($entry = readdir($handle))):
	        $entry = trim($entry);
	        if(!in_array($entry, $ignore)):
	            if(is_dir("$dir/$entry")):
	                $array[$entry] = explore($dir.'/'.$entry, $ignore);
	            endif;
	                
	        endif;
	    endwhile;
	    
	    rewinddir($handle);
	    
	    while(false !== ($entry = readdir($handle))):
	        if(!in_array($entry, $ignore)):
	            if(is_file($dir.'/'.$entry)):
	                $array[$entry] = $entry;
	            endif;
	        endif;
	    endwhile;
	    
	    closedir($handle);
	    
	    return $array;
	}


    /**
     * Encode a path (such as url_encode() function)
     * @param string    $path
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
     * (Note: Magic quotes deprecated as of PHP 5.3 and removed as of PHP 5.4)
     * 
     * @param mixed $input
     * @return mixed
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

    if(!function_exists('http_response_code')):
        /**
         * Generate header status (PHP < 5.4)
         * @param integer   $code
         * @return integer
        **/
        function http_response_code($code) {
            switch($code) {
                case 100: $message = 'Continue'; break;
                case 101: $message = 'Switching Protocols'; break;
                case 200: $message = 'OK'; break;
                case 201: $message = 'Created'; break;
                case 202: $message = 'Accepted'; break;
                case 203: $message = 'Non-Authoritative Information'; break;
                case 204: $message = 'No Content'; break;
                case 205: $message = 'Reset Content'; break;
                case 206: $message = 'Partial Content'; break;
                case 300: $message = 'Multiple Choices'; break;
                case 301: $message = 'Moved Permanently'; break;
                case 302: $message = 'Moved Temporarily'; break;
                case 303: $message = 'See Other'; break;
                case 304: $message = 'Not Modified'; break;
                case 305: $message = 'Use Proxy'; break;
                case 400: $message = 'Bad Request'; break;
                case 401: $message = 'Unauthorized'; break;
                case 402: $message = 'Payment Required'; break;
                case 403: $message = 'Forbidden'; break;
                case 404: $message = 'Not Found'; break;
                case 405: $message = 'Method Not Allowed'; break;
                case 406: $message = 'Not Acceptable'; break;
                case 407: $message = 'Proxy Authentication Required'; break;
                case 408: $message = 'Request Time-out'; break;
                case 409: $message = 'Conflict'; break;
                case 410: $message = 'Gone'; break;
                case 411: $message = 'Length Required'; break;
                case 412: $message = 'Precondition Failed'; break;
                case 413: $message = 'Request Entity Too Large'; break;
                case 414: $message = 'Request-URI Too Large'; break;
                case 415: $message = 'Unsupported Media Type'; break;
                case 500: $message = 'Internal Server Error'; break;
                case 501: $message = 'Not Implemented'; break;
                case 502: $message = 'Bad Gateway'; break;
                case 503: $message = 'Service Unavailable'; break;
                case 504: $message = 'Gateway Time-out'; break;
                case 505: $message = 'HTTP Version not supported'; break;
                default:
                    $code = 200;
                    $message = 'OK';
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1');
            header("$protocol $code $message", true, $code);
            return $code;
        }
    endif;
    
    /**
     * Check if the variable is a closure function
     * @param mixed     $function
     * @return boolean
    **/
    function is_function(&$variable) {
        return (is_object($variable) && ($variable instanceof Closure));
    }

?>