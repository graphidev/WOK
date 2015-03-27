<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Framework\Services;

    /**
     *
    **/
    class Database {

        /**
         * @var \PDO $interface   \PDO instance object
        **/
        private $interface;

        /**
         * Try to login and keep the interface.
         *
         * This method use the same parameters
         * as PDO::__construct()
         *
         * @see http://www.php.net/manual/en/pdo.construct.php
         *
         * @param string    $dsn            Data Source Name
         * @param string    $username       Database user's name
         * @param string    $password       Database user's password
         * @param array     $options        PDO login options
        **/
        public function __construct($dsn, $username = 'root', $password = '', $options = array()) {
            try {

                $this->interface = new \PDO($dsn, $username, $password, $options);
                $this->interface->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->interface->exec('SET NAMES UTF8');

            } catch(\Exception $e) {
                throw $e;
            }
            
        }


        /**
         * Execute a SQL query and return results.
         *
         * @param   string    $sql      SQL query
         * @param   array     $data     Query parameters' values
         * @param   int       $mode     Results fetching mode
         * @return  mixed     Return request results
        **/
        public function query($sql, $data = array(), $mode = \PDO::FETCH_ASSOC) {
            $query = $this->interface->prepare($sql);
            $query->execute($data);
            $output = $query->fetchAll($mode);
            $query->closeCursor();
            return $output;
        }


        /**
         * Execute a SQL query.
         * @param   string       $sql       SQL query
         * @param   array        $data      Query parameters' values
         * @return  boolean      Return result as boolean
        **/
        public function exec($sql, $data = array()) {
            $query = $this->interface->prepare($sql);
            $output = $query->execute($data);
            $query->closeCursor();
            return $output;
        }


        /**
         * Execute a query and output a single row.
         * This method will also try to get an single value as possible.
         *
         * @param   string    $sql      SQL query
         * @param   array     $data     Query parameters' values
         * @return  mixed     Returns
        **/
        public function single($sql, $data = array()) {
            $data = $this->query($sql, $data, \PDO::FETCH_ASSOC);
            $output = array_shift($data);

            if(count($output) == 1)
                $output = array_shift($output);

            return $output;
        }


        /**
         * Execute a query to get the count of results rows.
         * The query must contains "SELECT count(field) [AS ...] FROM"
         *
         * @param string    $sql        SQL Query
         * @param array     $data       Query parameters' value
         * @return integer
        **/
        public function total($sql, $data = array()) {
            $data = $this->query($sql, $data, \PDO::FETCH_COLUMN);
            $total = array_shift($data);
            return intval($total);
        }


        /**
         * Call a \PDO method without getting the object
         * @throws  LogicException          If the \PDO method is undefined
         * @param   string  $method         The \PDO method name
         * @param   array   $arguments      The \PDO method arguments
         * @return  mixed   The \PDO method returned value
        **/
        public function __call($method, $arguments) {
            if(!method_exists($this->interface, $method))
                 throw new \LogicException('PDO method '.$method.' does not exists');

            return call_user_func_array(array($this->interface, $method), $arguments);
        }


        /**
         * Destroy \PDO instance
        **/
        public function __destroy() {
            unset($this->interface);
        }

    }
