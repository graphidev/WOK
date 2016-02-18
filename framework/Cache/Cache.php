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
     * The Cache class provide an interface
     * to store, retrieve and remove values caching
    **/
    class Cache {

        protected $adapter;

        /**
         * Instanciate the cache interface
         * @param   \Cache\Adapter      $adapter            Cache adapter
        **/
        public function __construct(Adapters\AdapterInterface $adapter) {
            $this->adapter = $adapter;
        }

        /**
         * Store a value in the cache
         * @param   string      $key        Value key
         * @param   mixed       $data       Value to cache
         * @param   mixed       $lifetime   Caching life time
        **/
        public function store($key, $data, $lifetime) {
            $this->adapter->store($key, $data, $lifetime);
        }


        /**
         * Check the availability of a cached value
         * @param   string      $key        Cached value key
        **/
        public function exists($key) {
            return $this->adapter->exists($key);
        }


        /**
         * Retrieve a cached value
         * @param   string      $key        Cached value key
        **/
        public function fetch($key) {

            if(!$this->exists($key))
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
         * Remove all cached values
        **/
        public function clear() {
            return $this->adapter->clear(func_get_args());
        }


        /**
         * Allow specific adapter methods call
         * @param   string      $method         Adapter method's name
         * @param   string      $arguments      Adapter method's arguments
        **/
        public function __call($method, array $arguments = null) {

            if(!method_exists($this->adapter, $method))
                trigger_error('Undefined cache adapter method '.$method, E_USER_ERROR);

            return call_user_func_array([$this->adapter, $method], func_get_args());

        }


    }
