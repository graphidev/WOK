<?php

    
    /**
     * Numbers functions library
    **/
    

    /**
     * Check if a number is divisble by an other
    **/
    function is_divisible($number, $by) {
        if($by == 0) return false;
        return ($number % $by == 0 ? true : false);
    }

?>