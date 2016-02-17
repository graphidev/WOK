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
     * This file contains all the files system helpers functions.
     * Beware, some of them use some configuration constants
     * @package Core/Helpers/System
    **/

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
	 * @note 	Could returns false in case of in the lack of writing permission (errors generation disabled). The process is stopped at the first error.
    **/
    function rmpath($dir) {

        $data = scandir($path = $dir);

        foreach($data as $file) {
            if(!in_array($file, array('.', '..'))) {

                if(is_file($path.'/'.$file) || is_link($path.'/'.$file)) {
                    if(!@unlink($path.'/'.$file))
						return false;
                }

                elseif(is_dir($path.'/'.$file)) {
                    if(!rmpath($dir.'/'.$file))
						return false;

                }

            }
        }

        return @rmdir($path);

    }

?>
