<?php 
    
    /**
     * Utilities functions
     * This file contains some utilities functions
    **/


	/**
     * Return absolute URL path to the string parameter
    **/
	function path($string = null, $protocol = SYSTEM_PROTOCOL) {
		if(substr($string, 0, 1) == '/'):
			return $protocol.SYSTEM_DOMAIN.SYSTEM_DIRECTORY."$string";
		else:
			return $protocol.SYSTEM_DOMAIN.SYSTEM_DIRECTORY."/$string";
		endif;
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


    /**
     * Generate an URL accepted format from a string
    **/
    function nl2url($string) {
		// Accents \\
		$string = str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å',
									'î', 'ï', 'ì', 'í', 
									'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 
									'ù', 'û', 'ü', 'ú', 
									'é', 'è', 'ê', 'ë', 
									'ç', 'ÿ', 'ñ', 'ý',
									'À', 'Â', 'Ä', 'Á', 'Ã', 'Å',
									'Î', 'Ï', 'Ì', 'Í', 
									'Ô', 'Ö', 'Ò', 'Ó', 'Õ', 'Ø', 
									'Ù', 'Û', 'Ü', 'Ú', 
									'É', 'È', 'Ê', 'Ë', 
									'Ç', 'Ÿ', 'Ñ', 'Ý'
									),
								array(
									'a', 'a', 'a', 'a', 'a', 'a', 
									'i', 'i', 'i', 'i', 
									'o', 'o', 'o', 'o', 'o', 'o', 
									'u', 'u', 'u', 'u', 
									'e', 'e', 'e', 'e', 
									'c', 'y', 'n', 'y',
									'A', 'A', 'A', 'A', 'A', 'A', 
									'I', 'I', 'I', 'I', 
									'O', 'O', 'O', 'O', 'O', 'O', 
									'U', 'U', 'U', 'U', 
									'E', 'E', 'E', 'E', 
									'C', 'Y', 'N', 'Y'
								),
								$string
		);
		
		// Caractères spéciaux \\
		$string = preg_replace('#("|«|»)#', '', $string); // Guillemets
		$string = preg_replace('#(\[|\]|\{|\})#', '', $string); // Accolades et crochets
		$string = preg_replace('#(~|\#|\-|_)#', '', $string); // Tilde, dièse et autres
		$string = str_replace('$', 'dollars', $string); // Signe dollar
		$string = str_replace('@', '-at-', $string); // Arobase
		$string = str_replace('©', '-cr-', $string); // Copyright (C)
		$string = str_replace('®', '-r-', $string); // Marque déposée
		$string = str_replace('%', 'pc', $string); // Pourcentage
		$string = str_replace('\\', '', $string); // Anti-slash
		$string = str_replace(',', '', $string); // Virgule
		$string = str_replace('(', '', $string); // Parenthèse gauche
		$string = str_replace(')', '', $string); // Parenthèse droite
		$string = str_replace('\'', '', $string); // Apostrophe
		$string = str_replace('’', '', $string); // Apostrophe word
		$string = str_replace('^', '', $string); // Accent circonflexe
		$string = str_replace('/', '-', $string); // Slash
		$string = str_replace('.', '', $string); // Point
		
		// Symboles de ponctuation \\
		$string = preg_replace('#(\?|!|\;|:|&|\\*|\\+|<|>|=)#i', '', $string);
		
		// Conversion des espaces en tirents \\
		$string = str_replace(' ', '-', $string);
			
		// Remplacement de tirets de début/fin/multiples \\
		$string = preg_replace('#-{2,}#', '-', $string); // Plusieurs -> 1 seul
		//$string = preg_replace('#^-#', '', $string); // Tiret en début de chaine
		//$string = preg_replace('#-$#', '', $string); // Tiret en fin de chaine
		
		return urlencode($string);
	}
    

?>