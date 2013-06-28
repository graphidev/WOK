<?php

    /**
     * This file contains all the compatiblities functions for PHP
    **/


    /**
     * Return a cut string (a resume)
    **/
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
     * Remove Javascript actions in a string
    **/
    function strip_scripts($string, $exceptions = array()) {
		$events = array('onabort', 'onblur', 'onchange', 'onclick', 'ondblclick', 'ondrapdrop', 'onerror', 'onfocus', 'onkeydown', 'onkeypress', 'onkeyup', 'onload', 'onmouseover', 'onmouseout', 'onreset', 'onresize', 'onselect', 'onsubmit', 'onunload');
		
		foreach($events as $i => $name) {
			if(in_array($name, $exceptions)):
				unset($events[$i]);
			endif;
		}
		
        $string = preg_replace('#<script(.+)?>(.+)</script>#isU', '', $string);
		$string = preg_replace('# ('.implode('|',$events).')="(.+)"#isU', null, $string);
		$string = preg_replace('# ('.implode('|',$events).')=\'(.+)\'#isU', null, $string);
		
		return $string;
	}

?>