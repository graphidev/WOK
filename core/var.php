<?php

	/**
	 * Store and get global variables
	 * This use an internal variables set
	 * instead of superglobals.
	 *
	 * @package Core
	**/
	class Var {

		/**
		 * Globals stored variables
		**/
		private static $variables = array();


		/**
		 * Disable construction method
		 * This class have to been used statically
		**/
		private function __construct() {}


		/**
		 * Define or overwrite a variable
		 * @param 		string		$name	 	The variable's name
		 * @param 		mixed		$value		The value to set to the variable
		 * @return 		mixed					Return the defined variable's value (defined with $value)
		**/
		public static function set($name, $value) {

			return self::$variables[$name] = $value;

		}


		/**
		 * Define or overwrite a variable
		 * @param 		string		$name	 	The variable's name
		 * @return 		mixed					Return the variable's value
		**/
		public static function get($name) {

			return self::$variables[$name];
			
		}


		/**
		 * Check wether a variable exists or not
		 * @param 		string		$name	 	The variable's name
		 * @return 		boolean					Return wether the variable exists or not
		**/
		public static function exists($name) {

			return isset(self::$variables[$name]);

		}


		/**
		 * Destroy a variable if it exists
		 * @param 		string		$name	 	The variable's name
		 * @return 		boolean					Return wether the variable exists or not
		**/
		public static function clear($name) {

			if(self::exists($name))
				unset(self::$variables[$name]);

			return (!self::exists($name));

		}

	}
