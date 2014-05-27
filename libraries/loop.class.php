<?php
    
    /** 
     * Data loop manager
     *
     * @version 1.0
     * @require PHP 5.4+
     * @package Libraries
    **/
    class Loop implements Iterator {
        
        private $entries    = array();
        private $position   = 0;
        
        private $options = array(
            'recursive' => false,
            'parser' => false
        );
                
        
        /**
         * Define data and options
         * @param array     $data
         * @param array     $options
        **/
        public function __construct($data, $options = array()) {
            $this->rewind();
            $this->entries = is_array($data) ? $data : array($data);
            $this->options = array_merge($this->options, $options);
        }
        
        
        /**
         * Check for new iteration existence
         * @return boolean  True on available entry, false otherwise
        **/
        public function have_entries() {
			return $this->valid();   
        }

        
        /**
         * Return a formated date/time
         * Require a pre-formated datetime (e.g: YYYY-MM-DD)
         * Timestamp not allowed
         * @param string    $format
         * @param string    $field
         * @return string   Formated date from a field
        **/
        public function date($format = 'Y-m-d H:i:s', $field = null) {
            $time = (!empty($field) ? $this->field($field) : $this->entry());
            $timezone = new DateTimeZone(SYSTEM_TIMEZONE);
            $datetime = new DateTime(intval($time), $timezone);
            return $datetime->format($format);           
        }
        
        /**
         * Return the current entry
         * @return mixed
        **/
        public function entry($recursive = false) {
            if(($this->options['recursive'] || $recursive) && is_array($this->current()))
                return new Loop($this->current(), $this->options);
            else
                return $this->_parse($this->current());   
        }
         
        
        /**
         * Return a field of the current entry
         * @param string    $name
         * @return mixed
        **/
        public function field($name, $default = null) {
            return $this->_parse(array_value($name, $this->current(), $default));   
        }
        
        
        /**
         * Return the current position index
         * @param boolean   $increment
        **/
        public function index($increment = false) {
            return $increment ?  $this->key()+1 : $this->key();   
        }
        
        /**
         * Parse data according to parser option
         * @param array     $data
         * @return data
        **/
        private function _parse($data) {
            if(!empty($this->options['parser']) && is_callable($this->options['parser'], false))
                return call_user_func($this->options['parser'], $data);
                
            else
                return $data;
        }   
        
        
        /**
         * Default loop iteration methods.
         * These methods are the implementation of
         * the Iterator interface
        **/
        
        /**
         * Set cursor to the next entry
        **/
        public function next() {
            $this->position++;   
        }
        
        /**
         * Reset cursor position
        **/
        public function rewind() {
            $this->position = 0;
        }

        /**
         * Get current iterated value
         * @return mixed    Array's entry data
        **/
        public function current() {
            return $this->entries[$this->position];
        }
        
        /**
         * Get current iterated key
         * @return mixed    Array's entry key
        **/
        public function key() {
            return $this->position;
        }
        
        /**
         * Check if the current entry exists
         * @return boolean      True if the entry exists, false otherwise
        **/
        public function valid() {
            return isset($this->entries[$this->position]);
        }
   
    } 

?>