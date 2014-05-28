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
     *
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

?>