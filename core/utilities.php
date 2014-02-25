<?php 
    
    /**
     * Utilities functions
     * This file contains some utilities functions
    **/


	/**
     * Return absolute URL path to the string parameter
    **/
	function path($string = null, $domain = SYSTEM_DOMAIN, $protocol = SYSTEM_PROTOCOL, $port = null) {
        
        $domain = str_replace('~', SYSTEM_DOMAIN, $domain);  
        if(!empty($port)) $port = ":$port";
        $path = (substr($string, 0, 1) == '/' ? $string : "/$string");
        
        return "$protocol://$domain$port".SYSTEM_DIRECTORY."$path";
	}
	

	/**
     * Return absolute path from the server root
    **/
	function root($str = null) {
        // Windows server compatibility
        $str = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $str);
        
		return SYSTEM_ROOT.$str;
	}
    

    /**
     * Order array by $index
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
     * Parse an array to XML
    **/
    function xml_encode($array, $xml){
        if(!is_object($xml))
            $xml = new SimpleXMLElement("<$xml/>");
        
        foreach($array as $key => $value) {
            if(is_array($value)):
                toXML($value, $xml->addChild($key));
            else:
                $xml->xml_encode($key, $value);
            endif;
        }

        return $xml->asXML();
    }



    
    /**
     * Analyse a folder and return files and subfolders
     * Return an array like :
     *
     * Array(
     *      [foldername] => Array(
     *           [subfoldername] => Array( ... ),
     *        [filename],
     *          ...
     *      ),
     *      [filename],
     *      [filename],
     *      ...
     * )
     *
	**/
	function tree($dir) {
        if(!file_exists($dir)) return false;
        
	    $handle = opendir($dir);
	    $array = array();
	    
	    while(false !== ($entry = readdir($handle))):
	        $entry = trim($entry);
	        if(!preg_match('#^(\.|\.\.|\.DS_Store$)#is', $entry)):
	            if(is_dir("$dir/$entry")):
	                $array[$entry] = tree($dir.'/'.$entry);
	            endif;
	                
	        endif;
	    endwhile;
	    
	    rewinddir($handle);
	    
	    while(false !== ($entry = readdir($handle))):
	        if(!preg_match('#^(\.|\.\.|\.DS_Store$)#is', $entry)):
	            if(is_file($dir.'/'.$entry)):
	                $array[$entry] = $entry;
	            endif;
	        endif;
	    endwhile;
	    
	    closedir($handle);
	    
	    return $array;
	}
    

?>