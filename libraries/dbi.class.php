<?php
    
    /**
     * Allow to define, get and destroy globaly database instances.
     * DBIStatement define some functions which simplify query outputs  
     *
     * @version 3.0
     * @package Libraries
    **/
    class DBI {
        
        protected static $databases = array();
        protected static $interfaces  = array();
        
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
         * @param string    $database
        **/ 
        public function __construct($dsn, $username = 'root', $password = '', $options = array(), $database = 'default') {
            try {
                
                self::$databases[$database] = new PDO($dsn, $username, $password, $options);
                self::$databases[$database]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$interfaces[$database] = new DBIStatement($database);
                self::$interfaces[$database]->exec('SET NAMES UTF8');
                
            } catch(Exception $e) {
                throw new Exception('[PDO:'.$query->errorCode().'] '.$query->errorInfo(), $query->errorCode(), $e); 
            }
            
            if($database != 'default'):
                try {
                    self::$interfaces[$database]->exec("USE $database");
                } catch(Exception $e) {
                    throw $e;   
                }
            endif;
        }
        
        
        /**
         * Get extedend or classic database interface.
         * Define $statement as false to get the PDO interface
         *
         * @param string    $name
         * @param boolean   $statement
        **/
        public static function database($name = 'default', $statement = true) {
            if(!isset(self::$interfaces[$name]))
                throw new ExtendedLogicException("Undefined database $name", array('database'=>$name));
            
            return ($statement ? self::$interfaces[$name] : self::$databases[$name] );
        }
        
        
        
        /**
         * Disable interface and database. 
         * For security reasons, it is advised to call 
         * this method one every request is done.
         *
         * @param string    $name
        **/
        public static function close($name = 'default') {
           unset(self::$interfaces[$name]);
        }
        
    }
    
    /**
     * This class is a part of DBI class. 
     * However, it also can be used as standalone.
     *
     * This class contains all query helper functions.
     *
     * @package Libraries
    **/
    class DBIStatement extends DBI {
        
        private $database;
        
        /**
         * Define the interface to use
         * @param mixed    $database
        **/
        public function __construct($database) {
            if(is_object($database) && $database instanceof PDO)
                $this->dabatase = $database;
            else
                $this->database = parent::$databases[$database];
        }
        
        /**
         * Execute a query return result as boolean.
         *
         * @param string    $sql
         * @param array     $data
         * @return boolean
        **/
        public function exec($sql, $data = array()) {
            $query = $this->database->prepare($sql);
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
            $query = $this->database->prepare($sql);
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
        
    }

?>