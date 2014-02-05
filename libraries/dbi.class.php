<?php
    
    class DBI {
        
        protected static $interface = null;
        
        const DRV_MYSQL = 'mysql:host=%s';
        const DRV_PGSQL = 'pgsql:host=%s';
        const DRV_SQLTE = 'sqlite:%s';
      
        
        /**
         * Try to login and keep the interface
        **/ 
        public static function login($host, $username, $password = null, $options = array(), $driver = DBI::DRV_MYSQL) {
            try {
                $dsn = sprintf($driver, $host);
                self::$interface = new PDO($dsn, $username, $password, $options);
                self::$interface->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::exec('SET NAMES UTF8');
            } catch(Exception $e) {
                throw new Exception('[PDO:'.$query->errorCode().'] '.$query->errorInfo(), $query->errorCode(), $e); 
            }
        }
        
        /**
         * Disable current interface
        **/
        public static function logout() {
           self::$interface = null;    
        }
        
        /**
         * Set the database to use
        **/
        public function database($database) {
            try {
                self::exec("USE $database");
            } catch(Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);   
            }
        }
        
        /**
         * Execute a query and return the number of affected entries
        **/
        public static function exec($sql, $values = array()) {
            self::_checkInterface();
            
            $query = self::$interface->prepare($sql);
            $output = $query->execute($values);
                
            if(!$output)
                throw new Exception('[PDO:'.$query->errorCode().'] '.$query->errorInfo(), $query->errorCode(), $e); 

            $query->closeCursor();
            return $output;
        }
        
        /**
         * Execute a query and return output
        **/
        public static function query($sql, $values = array(), $output = PDO::FETCH_ASSOC) {
            $query = self::$interface->prepare($sql);
            $output = $query->execute($values);
                
            if(!$output)
                throw new Exception('[PDO:'.$query->errorCode().'] '.$query->errorInfo(), $query->errorCode(), $e);
            else
                $output = $query->fetchAll($output);
                
            $query->closeCursor();
            return $output;
        }
        
        /**
         * Check interface existence
        **/
        private static function _checkInterface() {
            if(!empty(self::$interface))
                return true;
            else
                throw new Exception('DBI : interface not available');
        }
        
    }

?>