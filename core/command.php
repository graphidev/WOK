<?php

	class Command implements Entrypoint {

		private static $name 	   = null;
		private static $command    = null;
		private static $action     = null;
		private static $parameters = array();
		private static $options    = array(
			'-l' => 'l:',	// Language option
		);

		/**
		 * Initialize command informations
		 * @param	string		$entrypoint			Command used to access
		 * @param	array		$segments			Segments of the command (space separated)
		**/
		public static function init($argv) {
			self::$name = $argv[0];
			$segments = array_slice($argv, 1);

			// Parse command segments
			foreach($segments as $key => $segment) {

				// Get the action (first non-parameter segment)
				if(substr($segment, 0, 1) != '-' && strpos($segment, ':') !== false) {
					self::$action = $segment;
				}

				// Set parameters
				if(substr($segment, 0, 2) == '--') {

					if(($pos = strpos($segment, '=')) !== false && $pos != strlen($segment)) {
						list($name, $value) = explode('=', $segment, 2);
					}

					else {

						$name = $segment;

						if(isset($segments[$key+1]))
							$value = $segments[$key+1];
						else
							$value = true;

					}

					// Remove quotes
					if(in_array(($quote = substr($value, 0, 1)), array('"', '\'')) && substr($value, -1) == $quote)
						$value = substr($value, 1, -1);

					self::$parameters[substr($name, 2)] = $value;

				}
			}

			/*
			$parameters = Router::parameters(self::$action);
			self::$parameters = getopt('', $parameters);
			*/

			$options = getopt('l:');

			if(!empty($options['l']))
				parent::language($options['l']);

		}

		public static function input($ask) {

		}

		public static function parameters() {
			return self::$parameters;
		}

		public static function parameter($name) {
			if(!isset(self::$parameters[$name]))
				return false;

			return self::$parameters[$name];
		}

		public static function action() {
			return self::$action;
		}

	}
