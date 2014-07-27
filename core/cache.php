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
        
        /**
         * Get cache file path and create necessary subfolders
         * @param string     $file      Cache file
        **/
        private static function path($file) {
            return root(PATH_CACHE.'/'.Session::get('language').'-'.$file);   
        }
        
        /**
         * Generate a cache file. Replace previous content if still exists
         * @param   string      $file       Cache file name
         * @param   mixed       $data       Data to store in the file
        **/
        public static function register($file, $data) { 
            $path = self::path($file);                
            makedir(dirname($path));
            file_put_contents($path, $data);
        }
        
        /**
         * Check if a cache file is available. It also can check if the file is up to date
         * @param string    $file     Cache file name
         * @param integer   $time     Cache file living time
        **/
        public static function exists($file, $time = 0) {
            return (file_exists($path = self::path($file)) && (empty($time) || (!empty($time) && filemtime($path) < time()+$time)));
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
         * Get file cache content
         * @param string    $file   The file cache name
        **/
        public static function get($file) {
            if(!self::exists($file))
                trigger_error("Cache file $file doesn't exists", E_USER_ERROR);
            
            readfile(self::path($file));
        }
        
        
        public static function destroy($file) {}
        public static function clean($file) {}
        
        
    }

?>