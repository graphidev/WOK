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
    class APCCache implements AdapterInterface {

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

            return apc_exists($key);

        }


        /**
         * Get cached value
         * @param       $key          string      Item identifier
         * @return                boolean     Return weither the item is available or not
        **/
        public function fetch($key) {

            return apc_fetch($key);

        }


        /**
         * Delete a cached value
         * @param   string      $key        Cached value key
        **/
        public function delete($key) {

            return apc_delete($key);

        }

        /**
         * Clear file cache
         * @note This method is not part of the AdapterInterface
        **/
        public function clear() {

            return apc_clear_cache();

        }

    }
