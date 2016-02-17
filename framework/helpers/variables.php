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
     * This file contains all the variables helpers functions.
     * @package Core/Helpers/Variables
    **/


    /**
     * Check if the variable is a closure function
     * @param 	mixed     $variable		Variable to check
     * @return 	boolean					Returns wether the variable is a closure function or not
    **/
    function is_closure(&$variable) {
        return (is_object($variable) && ($variable instanceof Closure));
    }


    /**
     * Check if it is a function either a closure
	 * @param 	mixed		$variable		Variable to check
	 * @return 	boolean						Returns wether the variable is a closure or a function name either none of these.
    **/
    function is_function(&$variable) {
        return is_callable($variable)  || is_closure($variable);
    }


    /**
	 * Get the boolean value of a variable (including string interpretation)
	 * @param 	mixed	$var	Source boolean variable
	 * @return 	Returns the boolean value of the given variable
	**/
	function realboolval($var) {

		$bool = filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

		if(is_null($bool))
			return boolval($var);

		return $bool;
	}

?>
