<?php
    
    class Loop {
        
        private $options = array(
            'recursive' => false,
            'parser' => false
        );
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
            if($this->options['recursive'] && is_array($this->entries[$this->position]))
                return new Loop($this->entries[$this->position], $this->options);
            else
                return $this->_parse($this->entries[$this->position]);   
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
         * Require a pre-formated datetime (e.g: YYYY-MM-DD)
         * Timestamp not allowed
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
            return $this->_parse($this->entries[$this->position][$name]);   
        }
        
        /**
         * Parse data according to parser option
        **/
        private function _parse($data) {
            if(!empty($this->options['parser']) && is_callable($this->options['parser'], false))
                return $this->options['parser']($data);
                
            else
                return $data;
        }
        
   
    }

?>