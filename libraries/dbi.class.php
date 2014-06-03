<?php
    
    /**
     * Allow to define, get and destroy globaly database instances.
     * It also contains some queries helpers functions.
     *
     * @version 4.1
     * @package Libraries
    **/
    class DBI {
        
        private $instance;
        
        private static $interfaces  = array();
        
        
        /**
         * Get extedend or classic database interface.
         * Define $statement as false to get the PDO interface
         *
         * @param string    $name
         * @param boolean   $statement
        **/
        public function __construct($name = null) {
            if(!empty(self::$interfaces)) {
                if(empty($name)) {
                    reset(self::$interfaces);
                    $this->instance = current(self::$interfaces);
                }
                elseif(isset(self::$interfaces[$name])) {
                      $this->instance = self::$interfaces[$name]; 
                }
                else {
                    throw new ExtendedLogicException("Undefined database $name", array('database'=>$name));
                }
            }
            else {
                throw new ExtendedLogicException("At least one database interface must be instantiated"); 
            }
        }
        
        
        /**
         * Try to login and keep the interface. 
         *
         * This method use the same parameters 
         * as PDO::__construct()
         *
         * @example http://www.php.net/manual/en/pdo.construct.php
         *
         * @param string    $dsn
         * @param string    $username
         * @param string    $password
         * @param array     $options
         * @param string    $name
        **/ 
        public static function open($dsn, $username = 'root', $password = '', $options = array(), $name = 'default') {
            try {
                
                self::$interfaces[$name] = new PDO($dsn, $username, $password, $options);
                self::$interfaces[$name]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$interfaces[$name]->exec('SET NAMES UTF8');
                
            } catch(Exception $e) {
                throw $e; 
            }
            
            try {
                self::$interfaces[$name]->exec("USE $name");  
            } catch(Exception $e) {}
        }
        
        
        
        /**
         * Disable interface and database. 
         * For security reasons, it is advised to call 
         * this method once all requests are done.
         *
         * @param string    $name
        **/
        public static function close($name = 'default') {
           unset(self::$interfaces[$name]);
        }
        
        
        /**
         * Define the dabatase to use for the next requests
         * @param string    $database
        **/ 
        public function using($database) {
            try {
                $this->exec("USE $database");
            } catch(Exception $e) {
                throw $e;   
            }   
        }
        
        /**
         * Execute a query return result as boolean.
         *
         * @param string    $sql
         * @param array     $data
         * @return boolean
        **/
        public function exec($sql, $data = array()) {
            $query = $this->instance->prepare($sql);
            $output = $query->execute($data); 
            $query->closeCursor();
            return $output;
        }
        
        
        /**
         * Execute a query and return output
         *
         * @param string    $sql
         * @param array     $data
         * @return array
        **/
        public function query($sql, $data = array(), $mode = PDO::FETCH_ASSOC) {  
            $query = $this->instance->prepare($sql);
            $query->execute($data);
            $output = $query->fetchAll($mode);
            $query->closeCursor();
            return $output;
        }
        
        
        /**
         * Execute a query and output a single row.
         * This method will also try to get an single value as possible.
         *
         * @param string    $sql
         * @param array     $data
         * @return mixed
        **/
        public function single($sql, $data = array()) {
            $data = $this->query($sql, $data, PDO::FETCH_ASSOC);
            $output = array_shift($data);
            
            if(count($output) == 1)
                $output = array_shift($output);
            
            return $output;
        }
        
        
        /**
         * Execute a query to get the total.
         * The query must contains "SELECT count(field) [AS ...] FROM"
         *
         * @param string    $sql
         * @param array     $data
         * @return integer
        **/
        public function total($sql, $data = array()) {
            $data = $this->query($sql, $data, PDO::FETCH_COLUMN);
            $total = array_shift($data);
            return intval($total);
        }
        
        /**
         * Call a PDO method without getting the object
         * @throws  ExtendedLogicException  If the PDO method is undefined
         * @param   string  $method         The PDO method name
         * @param   array   $arguments      The PDO method arguments
         * @return  mixed   The PDO method returned value
        **/
        public function __call($method, $arguments) {
            if(!method_exists($this->instance, $method))
                 throw new ExtendedLogicException('PDO method does not exists', array('method'=>$method));
            
            try {
                return call_user_func_array(array($this->instance, $method), $arguments);
            } catch(Exception $e) {
                throw $e;   
            }
        }
            
        
    }

?>