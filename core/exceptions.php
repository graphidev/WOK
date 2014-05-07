<?php
    
    /**
     * Extended Exceptions
     * @require Exception (and SPL Exceptions)
     *
     * @package Core
    **/
    
    /**
     * Extended exception
    **/
    class ExtendedException extends Exception {
        
        protected $trace    = array();
        protected $data     = array();
        
        /**
         * Redefine __construct Exception method
         * @param (additional) array     $data
        **/
        public function __construct($message = null, array $data = array(), $code = 0, $previous = null) {
            $this->trace = $this->getTrace();
            $this->data = $data;

            parent::__construct($message, $code, $previous);
        }
        
        /**
         * Redefine protected $file and $line
         * @param $level
        **/
        public function setCallFromTrace($level = 1) {
            $level--;
            $this->file = $this->trace[$level]['file'];   
            $this->line = $this->trace[$level]['line'];
        }
        
        /**
         * Return defined data
         * @param string    $name
         * @return mixed
        **/
        public function getData($name = null) {
            if(!empty($name))
                return $this->data[$name];
            else
                return $this->data;
        }
        
        /**
         * Class name where the exception have been thrown
         * @return mixed
        **/
        public function getClass() {        
            if(!empty($this->trace[0]['class']))
                return $this->trace[0]['class'];
                
            else
                return false;
        }
        
        /**
         * Function name where the exception have been thrown
         * @return mixed
        **/
        public function getFunction() {
            if(!empty($this->trace[0]['function']))
                return $this->trace[0]['function'];
                
            else
                return false;
        }
        
        /**
         * Method name where the exception have been thrown
         * @return mixed
        **/
        public function getMethod() {
            $class = $this->getClass();
            $function = $this->getFunction();
            
            if(!$class || !$function)
                return false;
            
            return "$class::$function";
        }
        
        /**
         * Return string exception
         * @return string
        **/
        public function __toString() {
            return "[".get_parent_class($this)."] {$this->message} ({$this->file} : {$this->line}) : ".$this->getTraceAsString()." \n";   
        }
    }


    /**
     * Extended InvalidArgument exception
    **/
    class ExtendedInvalidArgumentException extends InvalidArgumentException {
        
        protected $trace    = array();
        protected $data     = array();
        
        /**
         * Redefine __construct Exception method
         * @param (additional) array     $data
        **/
        public function __construct($message = null, array $data = array(), $code = 0, $previous = null) {
            $this->trace = $this->getTrace();
            $this->data = $data;

            parent::__construct($message, $code, $previous);
        }
        
        /**
         * Redefine protected $file and $line
         * @param $level
        **/
        public function setCallFromTrace($level = 1) {
            $level--;
            $this->file = $this->trace[$level]['file'];   
            $this->line = $this->trace[$level]['line'];
        }
        
        /**
         * Return defined data
         * @param string    $name
         * @return mixed
        **/
        public function getData($name = null) {
            if(!empty($name))
                return $this->data[$name];
            else
                return $this->data;
        }
        
        /**
         * Class name where the exception have been thrown
         * @return mixed
        **/
        public function getClass() {        
            if(!empty($this->trace[0]['class']))
                return $this->trace[0]['class'];
                
            else
                return false;
        }
        
        /**
         * Function name where the exception have been thrown
         * @return mixed
        **/
        public function getFunction() {
            if(!empty($this->trace[0]['function']))
                return $this->trace[0]['function'];
                
            else
                return false;
        }
        
        /**
         * Method name where the exception have been thrown
         * @return mixed
        **/
        public function getMethod() {
            $class = $this->getClass();
            $function = $this->getFunction();
            
            if(!$class || !$function)
                return false;
            
            return "$class::$function";
        }
        
        /**
         * Return string exception
         * @return string
        **/
        public function __toString() {
            return "[".get_parent_class($this)."] {$this->message} ({$this->file} : {$this->line}) : ".$this->getTraceAsString()." \n";   
        }
    }

    
    /**
     * Extended Logic exception
    **/
    class ExtendedLogicException extends LogicException {
        
        protected $trace    = array();
        protected $data     = array();
        
        /**
         * Redefine __construct Exception method
         * @param (additional) array     $data
        **/
        public function __construct($message = null, array $data = array(), $code = 0, $previous = null) {
            $this->trace = $this->getTrace();
            $this->data = $data;

            parent::__construct($message, $code, $previous);
        }
        
        /**
         * Redefine protected $file and $line
         * @param $level
        **/
        public function setCallFromTrace($level = 1) {  
            $level--;
            $this->file = $this->trace[$level]['file'];   
            $this->line = $this->trace[$level]['line'];
        }
        
        /**
         * Return defined data
         * @param string    $name
         * @return mixed
        **/
        public function getData($name = null) {
            if(!empty($name))
                return $this->data[$name];
            else
                return $this->data;
        }
        
        /**
         * Class name where the exception have been thrown
         * @return mixed
        **/
        public function getClass() {        
            if(!empty($this->trace[0]['class']))
                return $this->trace[0]['class'];
                
            else
                return false;
        }
        
        /**
         * Function name where the exception have been thrown
         * @return mixed
        **/
        public function getFunction() {
            if(!empty($this->trace[0]['function']))
                return $this->trace[0]['function'];
                
            else
                return false;
        }
        
        /**
         * Method name where the exception have been thrown
         * @return mixed
        **/
        public function getMethod() {
            $class = $this->getClass();
            $function = $this->getFunction();
            
            if(!$class || !$function)
                return false;
            
            return "$class::$function";
        }
        
        /**
         * Return string exception
         * @return string
        **/
        public function __toString() {
            return "[".get_parent_class($this)."] {$this->message} ({$this->file} : {$this->line}) : ".$this->getTraceAsString()." \n";   
        }
    }

?>