<?php

	/**
	 * Parse request and define informations about it.
	 * Allows to get request informations anywhere.
	 *
	 * @package Core
	**/
	class Request implements Entrypoint {

		protected static $path;
		protected static $query;
		protected static $method		= 'GET';
		protected static $headers		= array();
		protected static $content		= null;


		/**
		 * Define request informations
		 * @TODO allow custom settings
		**/
		public function __construct(array $set = array()) {

			self::$query        = substr($_SERVER['REQUEST_URI'], strlen(SYSTEM_DIRECTORY));
			self::$path         = (strpos(self::$query, '?') ? strstr(self::$query, '?', true) : self::$query);
			self::$headers		= getallheaders();

			// Set method
			if($method = getenv('REQUEST_METHOD'))
				self::$method = mb_strtoupper( $method );

			// Set parameters
			self::$parameters = $_GET;
			if(empty(self::$parameters) && ($parameters = substr(self::$query, strlen(self::$path)+1))):
				foreach(explode('&', $parameters) as $i => $parameter) {
					@list($name, $value) = explode('=', $parameter);
					self::$parameters[$name] = (isset($value) ? $value : true);
				}

			endif;

			// Set inputs
			if (self::$method == 'POST' || self::$method == 'PUT') {
				self::$content = file_get_contents('php://input');
			}

			// Set language
			$languages = get_accepted_languages(explode(' ', SYSTEM_LANGUAGES));
			if(Cookie::exists('language', true) && in_array($language = Cookie::get('language'), $languages)) {
				parent::$language = $language;
			}
			elseif(Session::exists('language', true) && in_array($language = Session::get('language'), $languages)) {
				parent::$language = Session::get('language');
			}
			elseif(!empty($languages)) {
				parent::$language = array_shift($languages);
			}
			else {
				parent::$language = SYSTEM_DEFAULT_LANGUAGE;
			}

		}


		/**
		* Get request URI (without host)
		* @return string
		**/
		public static function path() {
			return self::$path;
		}


		/**
		 * Get request URI (without host)
		 * @return string
		 * @deprecated
		 * @see Request::path()
		**/
		public static function uri() {
			return self::path();
		}

		/**
		* Get request url (with protocol, host, and parameters)
		* @return string
		* @deprecated
		* @see Request::path()
		**/
		public static function url() {
			return self::$query;
		}

		/** @TODO build the function **/
		public static function scheme() {}


		/**
		 * Check or get method
		 * @param string $verify
		 * @return mixed (boolean, string)
		**/
		public static function method($validate = null) {
			if(!empty($validate))
				return ( mb_strtoupper($validate) == self::$method );

			return self::$method;
		}


		/**
		 * Get current domain
		 * @return string Access domain
		**/
		public static function domain() {
			return getenv('HTTP_HOST');
		}


		/**
		 * Get current port
		 * @return integer
		**/
		public static function port() {
			return getenv('SERVER_PORT');
		}

		/**
	 	 * Get the request port (e.g HTTP/1.1)
		 * @return string
		**/
		public static function protocol() {
			return getenv('SERVER_PROTOCOL');
		}


		/**
	 	 * Get server information
		 * @return string
		 * @see http://php.net/getenv
		**/
		public static function server($information) {
			return getenv($information);
		}


		/**
		 * Get request parameter's value
		 * @return string
		 * @see Superglobal $_GET variable
		**/
		public static function parameter($name) {
			if(!isset(self::$parameters[$name]))
				return false;
		}


		/**
		 * Get request header's value
		 * @return string
		 * @see http://php.net/getallheaders
		**/
		public static function header($name) {
			if(!isset( self::$headers[$name] ))
				return false;

			return self::$headers[$name];
		}

		/**
		 * Check weither the request is secured (SSL/TLS) or not
		 * @return boolean
		**/
		public static function secure() {
			return ( ($value = getenv('HTTPS')) && var_filter($value, FILTER_VALIDATE_BOOLEAN) );
		}


		/**
		 * Check XML HTTP Request life
		 * @return boolean
		**/
		public static function ajax() {
			return (($value = getenv('HTTP_X_REQUESTED_WITH')) && strtolower($value) == 'xmlhttprequest');
		}

		/**
		 * Get request range
		 * @return integer
		**/
		public static function range() {
			return (isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : false);
		}


		/**
		 * Get Post/Put content
		 * @return Returns a stream data flux
		**/
		public static function content() {
			return self::$content;
		}


		/**
		 * Call request methods statically
		 * @param 	string	$method			Method name
		 * @param 	string	$arguments		Method arguments (if there are some)
		 * @return 	mixed	Returns method's output value
		**/
		public static function __callStatic($method, $arguments) {

			if(!methods_exists($this, $method))
				trigger_error('Undefined Request::'.$method);

			return call_user_func_array(array($this, $method), $arguments);

		}


		/**
		 * @deprecated
		**/
		public static function format() {
			pathinfo(self::$path, PATHINFO_EXTENSION)
		}

	}
