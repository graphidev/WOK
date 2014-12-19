<?php

    /**
     * Functions autoloader that allow you to get 
     * functions access without calling file
    **/
    class Fn {
        
        /**
         * Load a defined library
         * @param string $library   The library name. Library files must have the following syntax : [name].library.php
        **/
        public static function load($library) {
            
            if(!file_exists($path = root(PATH_LIBRARIES .'/'. $library . '.library.php')))
                trigger_error('Call to undefined library '. $library .' within '. PATH_LIBRARIES, E_USER_ERROR);
            
            require_once $path;
        }
        
        /**
         * Call of a function : try to load function with a defined file name
         * Please note that `load` function name is reserved by the class for libraries loading
         * Furthermore, this could be used exceptionaly in order to keep system performance
         * Use as following :
         * Fn::$function([$arguments, ...])
         * @param string $function The function that is called
         * @param array  $arguments The arguments passed to the function
        **/
        public static function __callStatic($function, $arguments) {
            
            if(!function_exists($function) && file_exists($path = root(PATH_LIBRARIES .'/'. $function . '.function.php'))) {
                require_once $path;
            }
            
            if(!function_exists($function))
                trigger_error('Call to undefined function '.$function.'()', E_USER_ERROR);
            
            return call_user_func_array($function, $arguments);
            
        }
        
    }
