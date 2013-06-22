	<?php
/*
	MySQLib est une librairie PHP développée par GraphiDev (http://graphidev.fr)
	Celle-ci permet d'accéder à une base de données (MySQL uniquement) de la même façon
	que la librairie PDO.
	
	CONDITIONS D'UTILISATION :
	-------------------------
	Ce(tte) oeuvre est mise à disposition selon les termes de la Licence Creative Commons Paternité - Pas d'Utilisation Commerciale 2.0 France. (http://creativecommons.org/licenses/by-nc/2.0/fr/)
*/		
	class MySQLib {
		private $link;
		private $prepare;
		protected $query;
		
		//- ----------------------------------------------------------------
		// Fonctions primaires (PDO like)
		//- ----------------------------------------------------------------
		public function __construct($host, $username, $password, $database, $encode = 'UTF8') {
			$this->link = $this->connect($host, $username, $password);
			if(!$this->link):
				throw new Exception('('.mysql_errno().') '.mysql_error());
			else:
				if(!mysql_select_db($database, $this->link)):
					throw new Exception('('.mysql_errno().') '.mysql_error());
				else:
					mysql_query("SET NAMES $encode", $this->link);
				endif;
			endif;
		}
		
		private function connect($host, $username, $password) {
			return @mysql_connect($host, $username, $password);
		}
		
		
		// Code d'erreur de la dernière requête
		public function errorCode() {
			return mysql_errno($this->link);
		}
		
		// Information sur l'erreur de la dernière requête
		public function errorInfo() {
			return mysql_error($this->link);
		}
		
		// Retourne l'identifiant de la dernière ligne insérée
		public function lastInsertId() {
			mysql_insert_id($this->link);
		}
		
		// Fermeture de la connexion à la base de données
		public function close() {
			exit($link);
			mysql_close($this->link);
			
		}
		
		// Protection des paramètres
		public function quote($str) {
			return "'".mysql_real_escape_string("$str", $this->link)."'";
		}
		
		// Exécution d'une requête
		public function query($sql) {
			$this->query = mysql_query($sql, $this->link) or die($this->errorCode().':'.$this->errorInfo());
			return new MySQLibStatement($this->query, $this->link);
		}
		
		// Exécution d'une requête
		// retourne le nombre de ligne affectées
		public function exec($sql) {
			$this->query = $this->query($sql, $this->link) or die($this->errorCode().':'.$this->errorInfo());
			return mysql_affected_rows();
		}
		
		// Prépare une requête avant son exécution
		public function prepare($sql) {
			return new MySQLibStatement($sql, $this->link);
		}
	}
	
	// Librairie donnant les résultats
	class MySQLibStatement {
		private $query;
		private $prepare;
		private $link;
			
		public function __construct($query, $link) {
			$this->query = $query;
			$this->prepare = $query;
			$this->link = $link;
		}
		
		// Code d'erreur de la dernière requête
		public function errorCode() {
			return mysql_errno($this->link);
		}
		
		// Information sur l'erreur de la dernière requête
		public function errorInfo() {
			return mysql_error($this->link);
		}
		
		// Lie un paramètre à un nom de variable spécifique
		public function bindParam($parameter, $value) {
			if(empty($this->prepare)):
				exit('Prepare SQL query before using MySQLibStatement::bindParam()');
			elseif(is_int($parameter)):
				$position = $parameter;
				$this->query = (strpos($this->prepare, '?', $position)) ? substr_replace($this->prepare, "'".mysql_real_escape_string($value)."'",(int)strpos($this->prepare, '?', $position), strlen('?')) : $this->prepare;	
			else:
				$this->query = preg_replace("#:$parameter#", mysql_real_escape_string($value), $this->prepare);
			endif;
		}
		
		// Lie un paramètre à un nom de variable spécifique
		public function bindValue($parameter, $value) {
			if(empty($this->query)):
				exit('Prepare SQL query before using MySQLibStatement::bindParam()');
			elseif(is_int($parameter)):
				$position = $parameter;
				$this->query = (strpos($this->query, '?', $position)) ? substr_replace($this->query, "'".mysql_real_escape_string($value)."'",(int)strpos($this->query, '?', $position), strlen('?')) : $this->query;	
			else:
				$this->query = preg_replace("#:$parameter#", mysql_real_escape_string($value), $this->query);
			endif;
		}
		
		// Exécution une requête préparée
		public function execute($values = null) {
			if(empty($this->query)):
				exit('Prepare SQL query before using MySQLibStatement::execute()');
			elseif(is_array($values)):
				foreach($values as $key => $value) {
					$this->bindValue($key+1, $value);
				}
			elseif($values != null):
				exit('MySQLibStatement::execute() setting must be an array');
			endif;
			$this->query = mysql_query($this->query, $this->link) or die($this->errorCode().':'.$this->errorInfo());
		}
		
		// Retourne le nombre de lignes affectées par la dernière requête
		public function rowCount() {
			return mysql_affected_rows($this->link);
		}
		
		// Libère la mémoire
		public function closeCursor() {
			mysql_free_result($this->query);
		}
		
		// Retourne les résultats sous forme de tableau
		public function fetch() {
			return mysql_fetch_array($this->query);
		}
		
		// Retourne tout les résultats sous forme d'un tableau
		public function fetchAll() {
			$array = array();
			while($result = mysql_fetch_array($this->query)):
				array_push($array, $result);
			endwhile;
			
			return $array;
		}
		
		// Retourne les résultats sous forme d'object
		public function fetchObject() {
			return mysql_fetch_object($this->query);
		}
	}
	
?>