<?php
    
    class Database {
        
        protected static $interface;
        
        const PD0_MYSQL = 'mysql:host=%s';
        const PDO_PGSQL = 'pgsql:host=%s';
        const PDO_SQLTE = 'sqlite:%s'; 
        
        public function __construct($host, $username, $password, $options = array(), $driver = Database::PD0_MYSQL) {
            try {
                $dsn = sprintf($driver, $host);
                self::$interface = new PDO($dsn, $username, $password, $options);
                self::$interface->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::exec('SET NAMES UTF8');
            } catch(Exception $e) {
                throw new Exception('[PDO:'.$query->errorCode().'] '.$query->errorInfo(), $query->errorCode(), $e); 
            }
        }
        
        public static function target($database) {
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





    try {
        /*    
        if(!empty($_FILES)):
            exit($_SERVER['HTTP_USER_AGENT']);
            print_r($_FILES); exit();
        endif;
        
        include 'init.php';
        
        $url = 'https://www.google.fr/';
        //$url = 'http://google.fr';
        //$url = 'http://www.local.graphidev.fr';
        $url = 'http://wok.loc/core/database.php';
        
        $headers = array(
			'X-HTTP-Method-Override: GET',
            //'X-Custom-Header: test'
		);
        
        $curl = new cURL($url, $headers);
        $curl->cookies(root('/tmp-cookies.txt'));
        
        $data = array(
            'upload' => '@'.realpath('init.php')
        );
        $curl->send($data, cURL::METHOD_POST);
        $curl->exec();
        
        //print_r($curl->info());
        
        exit($curl->response());
        //*/
        
        
        new Database('localhost', 'root', ''); 
        Database::target('wok');
        
        $query = "
            SELECT * FROM posts AS p LIMIT 2
        ";
        
        $posts = Database::query($query);
        
        
        $query = "
            SELECT * FROM authors AS a
            INNER JOIN accounts AS u ON u.account_id = a.account_id
            WHERE (";
        foreach($posts as $i => $post) {
            if($i > 0) $query .= ' OR ';
            $query .= 'a.post_id = '.$post['post_id'];
        }
        $query .= "
            )
        ";
        
        //echo ($query);
        
        $authors = Database::query($query);
        
        
        //*
        $copy = $posts; $posts = array();
        foreach($copy as $i => $post) {
            $posts[$post['post_id']] = $post;
        }
        
        foreach($authors as $i => $author) {
            $posts[$author['post_id']]['authors'][] = $author;
        }
        //*/
        
        
        echo '<pre>'; print_r($posts); echo '</pre>';
        //echo '<pre>'; print_r($authors); echo '</pre>';
        
        
        

        
    } catch(Exception $e) {
        
        echo $e->getMessage();
        
    }
    

?>