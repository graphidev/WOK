<?php

    /**
     * Manage routes' actions queue and execute the best one of them
     *
     * @package Core
    **/
    class Controller {
        private static $queue;
        private static $state = true;        
        
        
        /**
         * Call a controller action
         * @param string    $controller
         * @param string    $action
         * @return mixed    controller's returned value
        **/
        public static function call($controller, $action = null) {
            $controller = "\\Controllers\\$controller";
            $controller = new $controller;
            if(!empty($action))
                return call_user_func(array($controller, $action));
        }
        
        
        /**
         * Assign a controller to a route. Conditions can be either a 
         * boolean, string, or a function (closure) and action can either
         * be a controller:action names or a closure function.
         * @param mixed     $conditions
         * @param mixed     $action
         * @param boolean   $strict
        **/
        public static function route($conditions, $action, $strict = false) {
            self::$queue[] = array($conditions, $action, $strict);
        }
        
        /**
         * Invoke the controllers' queue
        **/
        public static function invoke() {
            foreach(self::$queue as $index => $value){
                if(self::$state):
                    list($conditions, $action, $strict) = $value;
                    self::$state = !self::execute($conditions, $action, $strict);
                endif;
            }
        }
        
        /** 
         * Execute an assigned controller if the conditions are satisfied.
         * The queue will be stopped if the strict parameter's value is true
         * @param mixed     $conditions
         * @param mixed     $action
         * @param boolean   $strict
        **/
        private static function execute($conditions, $action, $strict = false) {
             if(is_bool($conditions)):
                if($conditions): 
                    return $action(Request::uri()) or $strict; 
                endif;
            
            elseif(is_string($conditions)):
                if($conditions == Request::uri()):
                    return $action(Request::uri()) or $strict;
                endif;
            
            elseif(is_callable($conditions)):
                if($conditions(Request::uri())):
                    return $action(Request::uri()) or $strict;
                endif;
            
            elseif(is_array($conditions)):
                $end = false;
                foreach($conditions as $index => $value ) {
                    $end = self::execute($value, $action, Request::uri());
                }
                return $end;
                        
            endif;
        }
        
    }

?>