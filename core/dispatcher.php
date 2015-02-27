<?php

	class Dispatcher {
	
		/**
		 * Prevent usage of the __construct method
		**/
		private function __construct() {}
		
		public static function run($controller, $parameters) {
			
			@list($module, $action) = explode(':', strtolower($controller));
			
			// Check controller
			if(!method_exists($class = "\\Controllers\\$module", $action)) // Undefined controller or action
				trigger_error("Undefined controller/action for this route : $module:$action", E_USER_ERROR);

			$controller = array(new $class, $action);
			
			$response = call_user_func_array($controller, $parameters);
			
			if(is_null($response))
				return Response::null();
			
			return $response;
		}
		
	}