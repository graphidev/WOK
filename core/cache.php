<?php

    /**
     * Store and get cache items
     *
     * @package Core
    **/
    class Cache {
        
        const CACHETIME_SHORT       = 360; // 1 minutes
        const CACHETIME_MEDIUM      = 216000; // 1 hour
        const CACHETIME_LONG        = 5184000; // 24 hours
		const CACHETIME_UNDEFINED	= 0;

		
        /**
         * Get cache file path and create necessary subfolders
         * @param string     $file      Cache file
        **/
        private static function path($file) {
            return root(PATH_CACHE.'/'.Session::get('language').'-'.$file);   
        }
		
		/**
		 * Register data and return it if
		 * the cache file doesn't exists
		**/
		public static function register($file, $time, $data) {
			if(self::exists($file, $time))
				return self::get($file);
			else
				return self::put($file, call_user_func($data));
		}
        
        /**
         * Check if a cache file is available. It also can check if the file is up to date
         * @param string    $file     Cache file name
         * @param integer   $time     Cache file living time
        **/
        public static function exists($file, $time = 0) {
            return (file_exists($path = self::path($file)) && (empty($time) || (!empty($time) && (time() < filemtime($path)+$time) > 0)));
        }
        
        /**
         * Get cache file update time
         * @param   string  $file       Cache file name
        **/
        public static function time($file) {
            if(!self::exists($file))
                trigger_error("Cache file $file doesn't exists", E_USER_ERROR);
            
            return filemtime(self::path($file)); 
        }
        
		
		 /**
         * Generate a cache file. Replace previous content if still exists
         * @param   string      $file       Cache file name
         * @param   mixed       $data       Data to store in the file
        **/
		public static function put($file, $data) {
			$path = self::path($file);                
            mkpath(dirname($path));
            file_put_contents($path, $data);
			return $data;
		}
        
        /**
         * Get file cache content
         * @param string    $file   The file cache name
        **/
        public static function get($file) {
            if(!self::exists($file))
                trigger_error("Cache file $file doesn't exists", E_USER_ERROR);
            
            ob_start();
            
            readfile(self::path($file));
            
            $buffer = ob_get_contents();
            ob_end_clean();
            
            return $buffer;
            
        }
        
        /**
         * Remove a cached file
         * @param   string  $file   The cached file path
        **/
        public static function destroy($file) {
            if(self::exists($file))
                unlink(self::path($file));
        }
                
        
    }

?>