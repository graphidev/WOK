<?php

	/**
		MySQL (class)
		
		@version 1.0
		@required function strip_magic_quotes()
		
	*/

	class MySQL {
		private $table;
		protected static $interface;
        protected static $database;
        
        const FETCH_ARRAY = 'array';
        const FETCH_OBJECT = 'object';
        const FETCH_BOOL = 'boolean';
        const FETCH_COUNT = 'count';
        const FETCH_ID = 'id';
		
		/**
			@ new / construct
			@param (boolean) $connect = (required once, boolean) try to log with the current object
		*/
        // ($host, dbname, $user, $password, $charset, $interface)
		public function __construct($host, $user, $password, $charset = 'UTF8', $errors = true) {
            try {
				self::$interface = new PDO("mysql:host=$host;", $user, $password);
				self::$interface->query("SET NAMES '".$charset."'");
                
				self::$interface->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
						
				// ERROR_MODE : only for development versions
                if($errors)
				    self::$interface->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            }
			catch (Exception $e) {
                
                require_once(root(PATH_CORE.'/mysqlib.php'));
                try {
                    self::$interface = new MySQLib($host, $user, $password, '', $charset);
                }
                catch (Exception $error) {
                    exit("Database connection failed :: " . $error->getMessage());
                }
			}
            
		}
        
        public function database($database) {
            $query = self::$interface->query("USE $database");
            if($query)
                self::$database = $database;
            return $query;
        }
        
        public function close() {
            self::$interface = null;
        }
		
		// -----------------------------------
		
		/**
			@ execute/query
			@about execute an sql query and return results
			@param $sql = (string) sql query to execute. /!\ Not secured
			@return (mixed) depend of defined fetch mode
		*/
		public function execute($sql, $values = array(), $fetch = 'array') {
            
            if(is_array($values) && count($values) > 0):
                $query = self::$interface->prepare($sql);
                $query->execute($values);
            else:
                $query = self::$interface->query($sql);
            endif;
			
			if($fetch == self::FETCH_OBJECT):
				$data = $query->fetchObject();
			elseif($fetch == self::FETCH_BOOL):
				$data = ($query != false ? true : false);
			elseif($fetch == self::FETCH_ARRAY):
				$data = $query->fetchAll();
            elseif($fetch == self::FETCH_ID):
                $data = self::$interface->lastInsertId();
            else:
                return $query;
			endif;
            
            $query->closeCursor();
            return $data;
            
		}
		
		public function query($sql, $values = array(), $fetch = 'array') {
			return $this->execute($sql, $values, $fetch);	
		}
        
        public function createBase($name, $charset = 'UTF8', $collate = 'utf8_general_ci') {                        
            $query = self::$interface->query("CREATE DATABASE IF NOT EXISTS $name CHARACTER SET $charset COLLATE $collate");
            if($query)
                $this->database($name);
            return $query;
        }
        
        public function dropBase($name) {
            return self::$interface->query("DROP DATABASE IF EXISTS $name");
        }
        
        public function table($name) {
            return new MySQLTable($name);   
        }
        
        public function createTable($name, $columns) {
            if(is_array($columns))
                $columns = implode(',', $columns);
            
            return self::$interface->query("CREATE TABLE IF NOT EXISTS $name ($columns)");
        }
        
        public function dropTable($name) {
            return self::$interface->query("DROP TABLE IF EXISTS $name");   
        }
        
        
		
		// -----------------------------------
		
		/**
			@ quote
			@about apply quote() PDO method on the filled chain
			@param $string = (string) parameters of entries
			@return (string) transformed chain
		*/
		public function quote($string) {
			return self::$interface->quote($string);
		}

				
	}

    class MySQLTable extends MySQL {
        
        private $table;
        private $exists;
        private $columns = array();
        private $engine = 'MyISAM';
        private $charset = 'UTF8';
        private $collate = 'utf8_general_ci';
        
        public function __construct($table) {
            $this->table = $table;
        }
                    
        public function table_exists() {
            if(empty($this->exists)):
            
                $query = parent::$interface->query("SHOW TABLES LIKE '".$this->table."'");
                if(!$query):
                    $error = parent::$interface->errorInfo();
                    exit("MySQL error :: " .$error[2]. ' ('.parent::$interface->errorCode().')');
                endif;
                
                if(is_array($query->fetch(PDO::FETCH_ASSOC))):
                   $this->exists = true;
                else:
                   $this->exists = false;
                endif;
            endif;
                   
            return $this->exists;
        }
        
        public function addIntColumn($name, $length, $unsigned = false, $allow_null = true, $auto_increment = false, $primary = false, $default = null, $zerofill = false) {
            if($length <= 4):
                $type =  "TINYINT($length)";
            elseif($length  <= 6):
                $type =  "SMALLINT($length)";
            elseif($length <= 8):
                $type =  "MEDIUMINT($length)";
            elseif($length <= 11):
                $type = "INT($length)";
            else:
                $type = "BIGINT($length)";
            endif;
            
            $this->columns[] = array(
                'name' => $name,
                'type' => $type,
                'unsigned' => (!empty($unsigned) || !empty($primary) ? 'UNSIGNED' : null),
                'zerofill' => (!empty($zerofill) || !empty($unsigned) || !empty($primary) ? 'ZEROFILL' : null),
                'allow_null' => (!empty($allow_null) ? 'NULL' : 'NOT_NULL'),
                'default' => (!empty($default) ? "DEFAULT $default" : null),
                'auto_increment' => (!empty($auto_increment) ? 'AUTO_INCREMENT' : null),
                'primary' => (!empty($primary) ? 'PRIMARY KEY' : null),
            );
            
        }
                
        public function addBoolColumn($name, $default = null) {
            $this->columns[] = array(
                'name' => $name,
                'type' => 'TINYINT (1)',
                'unsigned' => 'UNSIGNED',
                'allow_null' => 'NULL',
                'default' => (!empty($default) ? "DEFAULT $default" : null)
            ); 
        }
        
        public function addStringColumn($name, $length = 255, $allow_null = true, $default = null) {
            
            $this->columns[] = array(
                'name' => $name,
                'type' => "VARCHAR($length)",
                'allow_null' => ($allow_null ? 'NULL' : 'NOT NULL'),
                'default' => (!empty($default) ? "DEFAULT $default" : null)
            );
                    
        }
        
        public function addTextColumn($name, $length = null, $allow_null = true) {
            if(empty($length)):
                $type = 'TEXT';
            elseif($length <= 255 || $length == 'small'):
                $type = 'VARCHAR ('.($length == 'small' ? 255 : $length).')';
            elseif($length <= 256 || $length == 'quick'):
                $type = 'TINYTEXT';
            elseif($length <= 65535 || $length == 'normal'):
                $type = 'TEXT';
            elseif($length <= 16777215 || $length == 'big'):
                $type = 'MEDIUMTEXT';
            else: // LONGTEXT - 4,294,967,295 (huge)
                $type = 'LONGTEXT';
            endif;
            
            $this->columns[] = array(
                'name' => $name,
                'type' => $type,
                'allow_null' => ($allow_null ? 'NULL' : 'NOT NULL'),
            );
        }
        
        public function addDecimalColumn($name, $length, $precision = 2, $unsigned = false, $default = null, $zerofill = false) {
            if($precision <= 24):
                $type = "FLOAT($length,$precision)";
            elseif($precison >= 25 && $precision <= 53):
                $type = "DOUBLE($length,$precision)";
            else:
                $type = "DECIMAL($length,$precision)";  
            endif;   
            
            $this->columns[] = array(
                'name' => $name,
                'type' => $type,
                'unsigned' => (!empty($unsigned) ? 'UNSIGNED' : null),
                'zerofill' => (!empty($zerofill) || !empty($unsigned) ? 'ZEROFILL' : null),
                'allow_null' => (!empty($allow_null) ? 'NULL' : 'NOT_NULL'),
                'default' => (!empty($default) ? "DEFAULT $default" : null),
            );
        }
        
        public function addBinaryColumn($name, $length = null, $allow_null = true) {
             if(empty($length)):
                $type = 'BLOB';
            elseif($length <= 256 || $length == 'quick'):
                $type = 'TINYBLOB';
            elseif($length <= 65535 || $length == 'normal'):
                $type = 'BLOB';
            elseif($length <= 16777215 || $length == 'big'):
                $type = 'MEDIUMBLOB';
            else: // LONGBLOB - 4,294,967,295 (huge)
                $type = 'LONGBLOB';
            endif;
            
            $this->columns[] = array(
                'name' => $name,
                'type' => $type,
                'allow_null' => ($allow_null ? 'NULL' : 'NOT NULL'),
            );
        }
        
        public function addColumn($name, $type, $options = null, $allow_null = true, $auto_increment = false, $primary = false, $default = null) {
            $this->columns[] = array(
                'name' => $name,
                'type' => $type,
                'options' => $options,
                'allow_null' => (!empty($allow_null) ? 'NULL' : 'NOT_NULL'),
                'default' => (!empty($default) ? "DEFAULT $default" : null),
                'auto_increment' => (!empty($auto_increment) ? 'AUTO_INCREMENT' : null),
                'primary' => (!empty($primary) ? 'PRIMARY KEY' : null),
            );   
        }
        
        public function renameColumn($name, $new_name) {
            $query = parent::$interface->query('SHOW COLUMNS FROM '.$this->table." LIKE '$name'");
            if(!$query):
                $error = parent::$interface->errorInfo();
                exit("MySQL error :: " .$error[2]. ' ('.parent::$interface->errorCode().')');
            endif;
            
            $column = $query->fetch(PDO::FETCH_ASSOC);
            
            return self::$interface->query("ALTER TABLE ".$this->table." CHANGE $name $new_name ".$column['Type']);
        }
        
        public function dropColumn($name) {
            return self::$interface->query("ALTER TABLE ".$this->table." DROP COLUMN $name");
        }
        
        public function create($charset = 'UTF8', $collate = 'utf8_general_ci', $engine = 'MyISAM') {
            $columns = null;
            foreach($this->columns as $i => $column) {
                 if($i > 0)
                    $columns .= ', ';
                $columns .= implode(' ', $column);
            }
            
            $this->columns = array();
            
            return parent::$interface->query("CREATE TABLE IF NOT EXISTS ".$this->table." ($columns) ENGINE=$engine CHARACTER SET $charset COLLATE $collate");
            
        }
        
        public function update($prev_column = 'LAST', $charset = 'UTF8', $collate = 'utf8_general_ci') {
            if($prev_column == 'LAST'):
                $columns = $this->getColumns();
                $total = count($columns)-1;
                $last = $columns[$total]['Field'];
            endif;
            
            $sql = 'ALTER TABLE '.$this->table;
            $total = count($this->columns);
            foreach($this->columns as $i => $column) {

                if($prev_column == 'LAST'):
                    $sql .= ' ADD  COLUMN '.implode(' ', $column)." AFTER $last ";
                elseif($prev_column == 'FIRST'):
                    $sql .=' ADD  COLUMN '.implode(' ', $column)." FIRST ";
                else:
                    $sql .= ' ADD  COLUMN '.implode(' ', $column)." AFTER $prev_column ";
                endif;
                
                if($i < $total)
                    $sql .= ', ';
                
            }
            
            $this->columns = array();
            $sql .= " CONVERT TO CHARACTER SET $charset COLLATE $collate, DEFAULT CHARACTER SET $charset COLLATE $collate ";
            return parent::$interface->query($sql);
            
        }
        
        public function remove() {
            $this->columns = array();
            return  parent::$interface->query('DROP TABLE IF EXISTS '.$this->table);
        }
        
        public function rename($name) {
            return parent::$interface->query("ALTER TABLE RENAME TO $name");   
        }
        

        public function getInfos() {
            $query = parent::$interface->query("SHOW TABLE STATUS WHERE Name='".$this->table."'");
            
            if(!$query):
                $error = parent::$interface->errorInfo();
                exit("MySQL error :: " .$error[2]. ' ('.parent::$interface->errorCode().')');
            endif;
            
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        
        public function getColumns() {
            $query = parent::$interface->query('SHOW COLUMNS FROM '.$this->table);
            if(!$query):
                $error = parent::$interface->errorInfo();
                exit("MySQL error :: " .$error[2]. ' ('.parent::$interface->errorCode().')');
            endif;

            $columns = array();
            while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {
               $columns[] = $rows;
            }
            
            return $columns;
        }
        
    }
?>