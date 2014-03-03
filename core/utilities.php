<?php 
    
    /**
     * Utilities functions
     * This file contains some utilities functions
    **/

    
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