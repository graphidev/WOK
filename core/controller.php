<?php

    /**
     * This is the controller class
     * All it's methods are statics
    **/

    class Controller {
        
        private static $queue       = array();
        private static $state       = true;
        
        public static function assign($conditions, $action, $strict = false) {
            self::$queue[] = array($conditions, $action, $strict);
        }
         
        private static function execute($conditions, $action, $query, $strict = false) {
             if(is_bool($conditions)):
                if($conditions): 
                    return $action($query) or $strict; 
                endif;
            
            elseif(is_string($conditions)):
                if($conditions == $query):
                    return $action($query) or $strict;
                endif;
            
            elseif(is_callable($conditions)):
                if($conditions($query)):
                    return $action($query) or $strict;
                endif;
            
            elseif(is_array($conditions)):
                $end = false;
                foreach($conditions as $index => $value ) {
                    $end = self::execute($value, $action, $query);
                }
                return $end;
            
            endif;
        }
        
        public static function invoke(&$request) {            
            foreach(self::$queue as $index => $value){
                if(self::$state):
                    list($conditions, $action, $strict) = $value;
                    self::$state = !self::execute($conditions, $action, $request, $strict);
                endif;
            
            }
            
        }
                
    }

?>