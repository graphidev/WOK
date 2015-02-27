<?php 

	class Logger {
		
		private static $logs = array();
			
		public static function log($message, $file = null, $line = null) {
			
			if(empty($file) || empty($line)) {
				$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
				$file = $backtrace[1]['file'];
				$line = $backtrace[1]['line'];
			}
			
			echo "$message in $file:$line";
			
		}
		
	}