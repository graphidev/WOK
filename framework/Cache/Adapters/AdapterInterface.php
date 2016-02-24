<?php

	/**
	* Web Operational Kit
	* The neither huger nor micro humble framework
	*
	* @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
	* @author	  Sébastien ALEXANDRE <sebastien@graphidev.fr>
	* @license	 BSD <license.txt>
	**/

	namespace Cache\Adapters;

	/**
	 * The Cache Adapter provide an interface
	 * class as guideline for cache adapters
	**/
	interface AdapterInterface {

		public function fetch($key);

		public function exists($key);

		public function store($key, $data, $lifetime);

		public function delete($key);

		public function clear();


	}
