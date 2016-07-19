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
     * The Cache Adapter provide an interface
     * class as guideline for cache adapters
    **/
    interface AdapterInterface {

        /**
         * Store an item data for defined life time
         * @param       $key          string      Item identifier
         * @param       $data         mixed       Item value to store
         * @param       $lifetime     integer     Item life time (null|0 for undefined)
         * @return      boolean       Return weither the item has been store or not
        **/
        public function store($key, $data, $lifetime = 0);


        /**
         * Check the availability of a cached item
         * @param       $key          string      Item identifier
         * @return      boolean       Return weither the item is available or not
        **/
        public function contains($key);


        /**
         * Get  cached value
         * @param       $key          string      Item identifier
         * @return      boolean       Return weither the item is available or not
        **/
        public function fetch($key);


        /**
         * Delete a specific cached value
         * @param   string      $key        Cached value key
        **/
        public function delete($key);


    }
