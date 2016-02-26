<?php

    /**
    * Web Operational Kit
    * The neither huger nor micro humble framework
    *
    * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @license     BSD <license.txt>
    **/

    namespace Cache\Adapters;

    /**
     * The Cache class provide an interface
     * to store, retrieve and remove values caching
    **/
    class FileCache implements AdapterInterface {

        const FILE_EXTENSION = 'tmp';

        protected $storage;
        protected $register;


        /**
         * Instanciate the file cache adapter
         * @param   string     $storage           Cache file storage path
        **/
        public function __construct($storage) {

            $this->storage = $storage;

            mkpath($this->storage);

            if(file_exists($register = $this->storage.'/register.json'))
                $this->register = json_decode(file_get_contents($register), true);

        }


        /**
         * Update register file
        **/
        public function __destruct() {

            if(!empty($this->register)) {
                file_put_contents($this->storage.'/register.json', json_encode($this->register));
            }

        }


        /**
         * Store a value in the cache
         * @param   string      $key        Value key
         * @param   mixed       $data       Value to cache
         * @param   mixed       $lifetime   Caching life time
        **/
        public function store($key, $data, $lifetime) {

            $this->register[$key] = array(
                'creation'  => time(),
                'lifetime'  => $lifetime,
                'etag'      => md5($data)
            );

            file_put_contents($this->storage.'/'.$key.'.'.FILE_EXTENSION, $data);

        }


        /**
         * Check the availability of a cached value
         * @param   string      $key        Cached value key
        **/
        public function exists($key) {

            // Not in the register
            if(!isset($this->register[$key]))
                return false;

            // Not readable file
            $filepath = $this->storage.'/'.$key.'.'.FILE_EXTENSION;
            if(!is_readable($filepath))
                return false;

            // Over time life
            $meta = $this->register[$key];
            if(time() > $meta['creation'] + $meta['lifetime']) {
                @unlink($filepath);
                return false;
            }

            // Check data integrity
            return (md5_file($filepath) == $meta['etag']);

        }


        /**
         * Retrieve a cached value
         * @param   string      $key        Cached value key
        **/
        public function fetch($key) {

            if(!$this->exists($key))
                return false;

            return file_get_contents($this->storage.'/'.$key.'.'.FILE_EXTENSION);

        }


        /**
         *
        **/
        public function getLastModifiedTime($key) {

            if(!$this->exists($key))
                return false;

            return $this->register[$key]['creation'];
            //return filemtime($this->storage.'/'.$key.'.'.FILE_EXTENSION);

        }


        /**
         * Delete a specific cached value
         * @param   string      $key        Cached value key
        **/
        public function delete($key) {

            if(isset($this->register[$key]))
                unset($this->register[$key]);

            return unlink($this->storage.'/'.$key.'.'.FILE_EXTENSION);
        }


        /**
         * Remove all cached values
        **/
        public function clear() {
            @rmpath($this->storage);
        }



    }
