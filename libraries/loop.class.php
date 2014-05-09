<?php
    /** 
     * Data loop manager
     *
     * @version 0.9
     *
     * @package Libraries
    **/
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
         * @param array     $data
         * @param array     $options
        **/
        public function __construct($data, $options = array()) {
            $this->entries = $data;
            $this->total = count($data);
            $this->options = array_merge($this->options, $options);
        }
        
        
        /**
         * Check if still have entries
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
         * Return a formated date/time
         * Require a pre-formated datetime (e.g: YYYY-MM-DD)
         * Timestamp not allowed
         * @param string    $format
         * @param string    $field
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
        public function entry() {
            if($this->options['recursive'] && is_array($this->entries[$this->position]))
                return new Loop($this->entries[$this->position], $this->options);
            else
                return $this->_parse($this->entries[$this->position]);   
        }
         
        
        /**
         * Return a field of the current entry
         * @param string    $name
         * @return mixed
        **/
        public function field($name) {
            return $this->_parse($this->entries[$this->position][$name]);   
        }
        
        
        /**
         * Return the current position index
         * @param boolean   $increment
        **/
        public function index($increment = false) {
            return $increment ? $this->position+1 : $this->position;   
        }
        
        
        /**
         * Return total number of entries
         * @return integer
        **/
        public function total() {
            return $this->total;   
        }
        
        
        /**
         * Parse data according to parser option
         * @param array     $data
         * @return data
        **/
        private function _parse($data) {
            if(!empty($this->options['parser']) && is_callable($this->options['parser'], false))
                return $this->options['parser']($data);
                
            else
                return $data;
        }
        
   
    }

?>