<?php

    /**
    * Web Operational Kit
    * The neither huger nor micro humble framework
    *
    * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @license     BSD <license.txt>
    **/

    namespace Cache;

    /**
     * The Cache class provides an interface
     * to manage data caching througt adapters.
    **/
    class Cache {

        /**
         * Cache system adapter
         * @var $adapter    AdapterInterface
        **/
        protected $adapter;


        /**
         * Instanciante cache adapter
         * @param   AdapterInterface    $adapter        Adapter implementing AdapterInterface
        **/
        public function __construct(Adapters\AdapterInterface $adapter) {
            $this->adapter = $adapter;
        }


        /**
         * Store an item data for defined life time
         * @param       $key          string      Item identifier
         * @param       $data         mixed       Item value to store
         * @param       $lifetime     integer     Item life time (null|0 for undefined)
         * @return      boolean     Return weither the item has been store or not
        **/
        public function store($key, $data, $lifetime = 0) {
            return $this->adapter->store($key, $data, $lifetime);
        }


        /**
         * Check the availability of a cached item
         * @param       $key          string      Item identifier
         * @return      boolean     Return weither the item is available or not
        **/
        public function contains($key) {
            return $this->adapter->contains($key);
        }


        /**
         * Alias of contains method
         * @param       $key          string      Item identifier
         * @return      boolean     Return weither the item is available or not
        **/
        public function exists($key) {
            return $this->contains($key);
        }



        /**
         * Get  cached value
         * @param       $key          string      Item identifier
         * @return                boolean     Return weither the item is available or not
        **/
        public function fetch($key) {

            if(!$this->contains($key))
                return false;

            return $this->adapter->fetch($key);

        }


        /**
         * Delete a specific cached value
         * @param   string      $key        Cached value key
        **/
        public function delete($key) {
            return $this->adapter->delete($key);
        }


        /**
         * Allow custom method adapter call
         * @param       $method         string          Method's name
         * @param       $arguments      array           Method's arguments
        **/
        public function __call($method, array $arguments = array()) {

            if(!method_exists($this->adapter, $method))
                trigger_error('Undefined cache adapter method '.get_class($this->adapter).'::'.$method, E_USER_ERROR);

            return call_user_func_array([$this->adapter, $method], $arguments);

        }


    }
