<?php

    /**
     * This is the controller class
    **/

    class Controller {
        private static $queue;
        private static $state = true;        
        
        /**
         * Call a controller action
        **/
        public static function call($controller, $action = null) {
            $controller = "\\Controllers\\$controller";
            $controller = new $controller;
            
            if(!empty($action))
                return $controller->$action();
        }
        
        /**
         * Assign a controller (function)
        **/
        public static function assign($conditions, $action, $strict = false) {
            self::$queue[] = array($conditions, $action, $strict);
        }
        
        /**
         * Invoke the queue
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
         * Execute an assigned controller
        **/
        private static function execute($conditions, $action, $strict = false) {
             if(is_bool($conditions)):
                if($conditions): 
                    return $action(Request::get('URI')) or $strict; 
                endif;
            
            elseif(is_string($conditions)):
                if($conditions == Request::get('URI')):
                    return $action(Request::get('URI')) or $strict;
                endif;
            
            elseif(is_callable($conditions)):
                if($conditions(Request::get('URI'))):
                    return $action(Request::get('URI')) or $strict;
                endif;
            
            elseif(is_array($conditions)):
                $end = false;
                foreach($conditions as $index => $value ) {
                    $end = self::execute($value, $action, Request::get('URI'));
                }
                return $end;
                        
            endif;
        }
        
    }

?>