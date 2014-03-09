<?php

    
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
        
        $path = (substr($path, 0, 1) == '/' ? $path : "/$path");
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
     * Get a multi-dimensional associative array value from a string
     *
     * @param   array   $array
     * @param   string  $path
     * @return  mixed   
    **/
    function array_value($array, $path) {
        if(!empty($path)) {
            
            $keys = explode('.', $path);
            foreach ($keys as $key) {
                
                if (isset($array[$key]))
                    $array = $array[$key];
                else
                    return null;
                
            }
            
        }
 
        return $array;
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

?>