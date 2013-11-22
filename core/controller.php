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
        public function call($controller, $action = null) {
            $controller = "\\Controllers\\$controller";
            $controller = new $controller;
            
            if(!empty($action))
                return $controller->$action();
        }
        
        /**
         * Assign a controller (function)
        **/
        public function assign($conditions, $action, $strict = false) {
            self::$queue[] = array($conditions, $action, $strict);
        }
        
        /**
         * Invoke the queue
        **/
        public function invoke() {
            foreach(self::$queue as $index => $value){
                if(self::$state):
                    list($conditions, $action, $strict) = $value;
                    self::$state = !$this->execute($conditions, $action, $strict);
                endif;
            }
        }
        
        /** 
         * Execute an assigned controller
        **/
        private function execute($conditions, $action, $strict = false) {
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
                    $end = $this->execute($value, $action, Request::get('URI'));
                }
                return $end;
                        
            endif;
        }
        
    }

?>