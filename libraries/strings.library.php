<?php

    /**
     * This file contains all the treatments functions for PHP
     *
     * @package Libraries
    **/

    /**
     * Return a cut string after a number of words.
     *
     * @param string    $string     The string to cut
     * @param integer   $max        The max output words
     * @param string    $more       The after "more" characters 
     * @return string       $string or cut string
    **/
	function resume($string, $max = 50, $more = ' ...') {
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
			$resume .= $more;
		endif;
		return $resume;
	}

    /**
     * Return a cut string after a number or characters without breaking a word.
     
     * @inspiredby  http://www.creativejuiz.fr/blog/tutoriels/php-couper-une-phrase-sans-couper-un-mot
     *
     * @param string    $string     The string to cut
     * @param integer   $max        The max output words
     * @param string    $more       The after "more" characters 
     * @return string       $string or cut string
    **/
    function maxlength($string, $max, $more = ' ...') {
        if(strlen($string) <= $max) return $string;  
        $resume = mb_substr($string, 0, $max - strlen($more) + 1, 'UTF-8');
        return substr($resume, 0, strrpos($resume, ' ')) . $more;   
    }


    /**
     * Generate keywords from a string
    **/
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