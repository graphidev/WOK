<?php

	/**
	* Web Operational Kit
	* The neither huger no micro extensible framework
	*
	* @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
	* @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
	* @license     BSD <license.txt>
	**/

	namespace Framework\Core;

	/**
     * Store and get cached data
	**/
	class Cache {

		/**
		 * @var string 	$file		Cache file path
		**/
		private $file;

		/**
		 * @const PATH_CACHE		Cache storage folder path
		**/
		const PATH_CACHE = '/storage/cache';

		const CACHETIME_SHORT       = 360; // 1 minutes
        const CACHETIME_MEDIUM      = 216000; // 1 hour
        const CACHETIME_LONG        = 5184000; // 24 hours
		const CACHETIME_UNDEFINED	= 0;


		/**
		 * Set cache file path
		 * @param string	$path		Cache file relative path
		**/
		public function __construct($path) {
			$this->file = root(self::PATH_CACHE.'/'.$path);
		}

		/**
		 * Check weither the cache file exists or not
		 * @return Boolean
		**/
		public function exists() {
			return file_exists($this->file);
		}


		/**
		 * Know if the cache file is most recent than given parameters
		 * @note This function also apply Cache::exists() method before
		 * @param 	integer|string		$compare 		The information to compare to
		 * @return Boolean
		**/
		public function recent($compare/*, ...*/){



			if(!$this->exists())
				return false;


			if(\func_num_args()) {
				$lastupdate = filemtime($this->file);
				foreach(\func_get_args() as $compare) {
					if(
						(is_int($compare) && $lastupdate > time() + $compare)
						|| (is_string($compare) && file_exists($compare) && $lastupdate > filemtime($compare))
					) {
						return false;
					}
				}

			}

			return true;

		}

		/**
		 * Return the cached data
		 * @return 	Return the cached data or FALSE if the file doesn't exists
		**/
		public function get() {
			if($this->exists())
				return file_get_contents($this->file);

			else
				return false;

		}

		/**
		 * Register data in the cache file
		 * @param 	mixed 	$data		Data to store
		 * @return 	Return the cached data or FALSE if the data have not been stored
		**/
		public function put($data) {
			mkpath(dirname($this->file));
            $register = file_put_contents($this->file, $data);

			return ($register ? $data : false);
		}


		/**
		 * Register data in the cache file or return if they are stil available
		 * @param 	Closure 	$data		Closure that return data to store
		 * @param 	integer 	$time		Time that the cache file is available
		 * @return 	Return the cached data or FALSE if the file doesn't exists
		**/
		public function register(\Closure $data, $time = 0) {

			if($time && $this->recent($time))
				return $this->get();

			else
				return $this->put(call_user_func($data));

		}

		/**
		 * Remove cache file
		 * @return 	Boolean of the success of the operation
		**/
		public function remove(){
			if($this->exists())
				return unlink($this->file);
		}


		/**
		 * Clean partially or completely the cache files
		 * @note This function is independant of Cache class objects
		 * @note If the $folders argument is not set, then every the cache files and folders will be deleted
		 * @param 	array  	$folders	The folders to clean
		**/
		public static function clean(array $folders = array()) {
			if(!empty($folders)) {

				$deleted = false;
				foreach($folders as $path) {
					if(is_dir( $folder = root(self::PATH_CACHE.$path) )) {

						if(!rmpath($folder)) /* => */ throw new RuntimeException($folder);

					}
				}

			}
			else {

				return rmpath(root(self::PATH_CACHE));

			}
		}

	}
