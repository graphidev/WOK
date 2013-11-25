<?php
    
    class Database {
        
        protected static $interface;
        
        const PD0_MYSQL = 'mysql:host=%s';
        const PDO_PGSQL = 'pgsql:host=%s';
        const PDO_SQLTE = 'sqlite:%s'; 
        
        public function login($host, $username, $password, $options = array(), $driver = Database::PD0_MYSQL) {
            try {
                $dsn = sprintf($driver, $host);
                self::$interface = new PDO($dsn, $username, $password, $options);
                self::$interface->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::exec('SET NAMES UTF8');
            } catch(Exception $e) {
                throw new Exception('[PDO:'.$query->errorCode().'] '.$query->errorInfo(), $query->errorCode(), $e); 
            }
        }
        
        public function target($database) {
            try {
                self::exec("USE $database");
            } catch(Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);   
            }
        }
        
        public static function exec($sql, $values = array()) {
            $query = self::$interface->prepare($sql);
            $result = $query->execute($values);
                
            if(!$result)
                throw new Exception('[PDO:'.$query->errorCode().'] '.$query->errorInfo(), $query->errorCode(), $e); 

            $query->closeCursor();
            return $result;
        }
        
        public static function query($sql, $values = array(), $output = PDO::FETCH_ASSOC) {
            $query = self::$interface->prepare($sql);
            $result = $query->execute($values);
                
            if(!$result)
                throw new Exception('[PDO:'.$query->errorCode().'] '.$query->errorInfo(), $query->errorCode(), $e);
            else
                $result = $query->fetchAll($output);
                //$result = $query->fetchAll();
                
            $query->closeCursor();
            return $result;
        }
        
    }

?>