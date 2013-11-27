<?php

    class Loop {
        
        private $options = array();
        private $entries = array();
        public $position = 0;
        public $total = 0;
        
        
        /**
         * Define data and options
        **/
        public function __construct($data, $options = array()) {
            $this->entries = $data;
            $this->total = count($data);
            $this->options = array_merge($this->options, $options);
        }
        
        
        /**
         * 
        **/
        public function have_entries() {
			if($this->position < $this->total):
				return true;
			else:
				return false;
			endif;   
        }
        
        
        /**
         * Increment counter
        **/
        public function next_entry() {
            $this->position++;   
        }
        
        
        /**
         * Return the current entry
        **/
        public function entry() {
            return $this->entries[$this->position];   
        }
        
        /**
         * Return the current position index
        **/
        public function index($increment = false) {
            return $increment ? $this->position+1 : $this->position;   
        }
        
        /**
         * Return total number of entries
        **/
        public function total() {
            return $this->total;   
        }
        
        
        /**
         * Return a formated date/time 
         * Require a formated datetime (e.g: YYYY-MM-DD)
        **/
        public function date($format = 'Y-m-d H:i:s', $field = null) {
            $time = (!empty($field) ? $this->field($field) : $this->entry());
            $timezone = new DateTimeZone(SYSTEM_TIMEZONE);
            $datetime = new DateTime(intval($time), $timezone);
            return $datetime->format($format);           
        }
         
        
        /**
         * Return a field of the current entry
        **/
        public function field($name) {
            return $this->entries[$this->position][$name];   
        }
   
    }

?>