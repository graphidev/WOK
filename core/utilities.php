<?php 

// -----------------------------------------------------------	

	// Adresse absolue (URL)
	function path($string = null) {
		if(preg_match('#^/(.)*$#', $string)):
			return SITE_ADDR."$string";
		else :
			return SITE_ADDR."/$string";
		endif;
	}
	
// -----------------------------------------------------------	

	// Chemin absolu (racine)
	function root($str = null) { // Accès à la racine du site
        // Windows server compatibility
        $str = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $str);
        
		return SERVER_ROOT.$str;
	}

// -----------------------------------------------------------	
	
	// Valeur paramètre utilisateur connecté
	function session($setting) {
		$session = new session();
		return $session->get($setting);
	}
	
// -----------------------------------------------------------	
	
	// Suppression des magic_quotes
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
	
// -----------------------------------------------------------	
	
	// Retourne une chaine sans accents ni ponctuation
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

// -----------------------------------------------------------	
	
	// Transforme une chaine en paramètres REGEX
	function str2regex($str, $highlight = null) {
		if(!empty($highlight)):
			$str = str_replace("($highlight)", "(($highlight))", $str);
		endif;
		$str = str_replace('?', '\?', $str);
		$str = str_replace('(page|int)', '[0-9]?', $str);
		$str = preg_replace('#\([a-z]+\|iso\)#', '[a-z]{2,5}', $str);
		$str = preg_replace('#\([a-z]+\|int\)#', '[0-9]+', $str);
		$str = preg_replace('#\([a-z]+\|str\)#', '[_&a-z0-9\.\+-]+', $str);
		//	$str = preg_replace_callback('#\(date\|\)#', create_function('$matches', ''), $str);
		
		return $str;
	}

// -----------------------------------------------------------	

	function strip_scripts($string, $exceptions = array()) {
		$events = array('onabort', 'onblur', 'onchange', 'onclick', 'ondblclick', 'ondrapdrop', 'onerror', 'onfocus', 'onkeydown', 'onkeypress', 'onkeyup', 'onload', 'onmouseover', 'onmouseout', 'onreset', 'onresize', 'onselect', 'onsubmit', 'onunload');
		
		foreach($events as $i => $name) {
			if(in_array($name, $exceptions)):
				unset($events[$i]);
			endif;
		}
		
		$string = preg_replace('# ('.implode('|',$events).')="(.+)"#isU', null, $string);
		$string = preg_replace('# ('.implode('|',$events).')=\'(.+)\'#isU', null, $string);
		$string = preg_replace('#<script(.+)?>(.+)</script>#isU', '', $string);
		
		return $string;
	}

	
// -----------------------------------------------------------
	
	// Remove host additional root on routing
	function strip_host_root($url) {
		$root = preg_replace('#^(https?://(.+))(/(.+))$#', "$3", SITE_ADDR);
		return preg_replace("#^$root(.+)$#", "$1", $url);
	}
	
// -----------------------------------------------------------

	// Retourne le type MIME
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


// -----------------------------------------------------------

	function strip_accents($string) {
		return str_replace(array('à', 'â', 'ä', 'á', 'ã', 'å',
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
	}
	
	
// -----------------------------------------------------------

	function resume($string, $max = 50) { 
		$string = strip_tags($string);
		$array = explode(" ", $string);
		if(count($array) < $max):
			$resume = $string;
		else:
			$resume = null;
			for($i=0; $i<$max; $i++) {
				$resume .= ' '.$array[$i]; 
			}
		endif;
	   
		if(count($array) > $max):
			$resume .= ' ...';
		endif;
		return $resume; 
	}

// -----------------------------------------------------------

	function get_url_format($element) {
		if(isset($GLOBALS['rewriting'][$element])):
			if(SITE_URL_REWRITING):
				return $GLOBALS['rewriting'][$element]['rewrited'];
			else:
				return $GLOBALS['rewriting'][$element]['default'];
			endif;
		else:
			return false;
		endif;
	}
	
// -----------------------------------------------------------

	function strstr_before($haystack, $needle) {
	
		if(PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 3):
			return strstr($haystack, $needle, true);
		else:
			$after = strstr($haystack, $needle);
			return str_replace($after, '', $haystack);
		endif;
	
	}

// -----------------------------------------------------------

	function keywords($string, $sensitivity = 4, $min = 2, $max = 8, $limit = 10) {
	
		// Delete HTML tags, codes, punctuation ...
		$string = strip_tags($string);
		$string = preg_replace('#<(/)?(.+)(/)?>#', '', $string);
		$string = preg_replace('#([[:punct:]])#isU', '', $string);
		
		// Sort words
		$words = explode(' ', $string);
		$words = array_count_values($words);
		asort($words);
		$words = array_reverse($words);
		
		// Remove words where length < sensitivity
		foreach($words as $word => $count) {
			if(strlen($word) < $sensitivity):
				unset($words[$word]);
			endif;
		}
		
		// Total words
		$total = count($words);
		
		// Check words density
		foreach($words as $word => $count) {
			$density = ($count/$total)*100;
			if($density < $min || $density > $max):
				unset($words[$word]);
			endif;
		}
		
		// Apply the limit
		$words = array_splice($words, 0, $limit, false);
		
		$result = array();
		foreach($words as $word => $count) {
			$result[] = $word;
		}
		
		return $result;
	}
	
// -----------------------------------------------------------

	/*
	    Function tree()
	    
	    @param $dir (string)            Directory root path
	    
	    @return (array) Files and directories
	*/
	function tree($dir) {
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
	
// -----------------------------------------------------------


?>