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
     * The FileCache is an adapter based on
     * the file system for the Cache class.
    **/
    class FileCache implements AdapterInterface {


        /**
         * File caching extension
         * @constant    string
        **/
        const FILE_EXTENSION = 'tmp';

        /**
         * Storage path
         * @var string
        **/
        protected $storage;


        /**
         * Files cache register
         * @var array()
        **/
        protected $register = array();


        /**
         * Instanciate adapter storage path
         * @param   string   $path   Storage path
        **/
        public function __construct($path) {

            $this->storage = $path;

            // Load the storage path register
            if(file_exists($register = $this->storage.'/register.json'))
                $this->register = json_decode(file_get_contents($register), true);

        }


        /**
         * Store a data value for defined life time
         * @param       $key          string      Data identifier
         * @param       $data         mixed       Data value to store
         * @param       $lifetime     integer     Data life time (null|0 for undefined)
         * @return      boolean     Return weither the item has been store or not
        **/
        public function store($key, $data, $lifetime = 0) {

            $this->register[$key] = array(
                'creation'  => time(),
                'lifetime'  => $lifetime,
                'etag'      => md5($data)
            );

            mkpath($this->storage);

            return file_put_contents($this->storage.'/'.$key.'.'.self::FILE_EXTENSION, $data);

        }


        /**
         * Check the availability of a cached value
         * @param       $key          string      Value identifier
         * @return      boolean     Return weither the item is available or not
        **/
        public function contains($key) {

            // Not in the register
            if(!isset($this->register[$key]))
                return false;

            // Not readable file
            $filepath = $this->storage.'/'.$key.'.'.self::FILE_EXTENSION;
            if(!is_readable($filepath))
                return false;

            // Over time life
            $meta = $this->register[$key];
            if(!empty($meta['lifetime']) && time() > $meta['creation'] + $meta['lifetime']) {
                @unlink($filepath);
                return false;
            }

            // Check data integrity
            return (md5_file($filepath) == $meta['etag']);

        }


        /**
         * Get cached value
         * @param       $key          string      Item identifier
         * @return                boolean     Return weither the item is available or not
        **/
        public function fetch($key) {

            if(!$this->contains($key))
                return false;

            return file_get_contents($this->storage.'/'.$key.'.'.self::FILE_EXTENSION);

        }


        /**
         * Delete a cached value
         * @param   string      $key        Cached value key
        **/
        public function delete($key) {

            if($this->contains($key))
                return true;

            unset($this->register[$key]);

            return unlink($this->storage.'/'.$key.'.'.self::FILE_EXTENSION);

        }


        /**
         * Get the cache last modified time
         * @note This method is not part of the AdapterInterface
        **/
        public function getLastModifiedTime($key) {

            if(!$this->contains($key))
                return false;

            return $this->register[$key]['creation'];

        }

        /**
         * Clear file cache
         * @note This method is not part of the AdapterInterface
        **/
        public function clear() {

            $this->register = array();

            return @rmpath($this->storage);

        }


        /**
         * Update the current register
         * @note This method is not part of the AdapterInterface
        **/
        public function __destruct() {

            if(!empty($this->register)) {
                file_put_contents($this->storage.'/register.json', json_encode($this->register));
            }

        }


    }
