<?php

	namespace Core\Http;

	class Request extends Entrypoint {

		protected static $path;
		protected static $query;
		protected static $method		= 'GET';
		protected static $parameters 	= array();
		protected static $headers		= array();
		protected static $content		= null;

		// public function __construct(array $set = array())
		public function parse() {

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

		public static function path() {
			return self::$path;
		}

		/**
		 * @deprecated
		 * @see Request::path()
		**/
		public static function uri() {
			return self::path();
		}


		public static function scheme() {}

		public static function method($validate = null) {
			if(!empty($validate))
				return ( mb_strtoupper($validate) == self::$method );

			return self::$method;
		}

		public static function domain() {
			return getenv('HTTP_HOST');
		}

		public static function port() {
			return getenv('SERVER_PORT');
		}

		public static function protocol() {
			return getenv('SERVER_PROTOCOL');
		}

		public static function server($information) {
			return getenv($information);
		}

		public static function parameter($name) {
			if(!isset(self::$parameters[$name]))
				return false;
		}

		public static function header($name) {
			if(!isset( self::$headers[$name] ))
				return false;

			return self::$headers[$name];
		}


		public static function secure() {
			return ( ($value = getenv('HTTPS')) && var_filter($value, FILTER_VALIDATE_BOOLEAN) );
		}

		public static function xhr() {
			return (($value = getenv('HTTP_X_REQUESTED_WITH')) && strtolower($value) == 'xmlhttprequest');
		}

		public static function ajax() {
			return self::xhr();
		}


		/**
		 * Inputs
		**/
		public static function content() {
			return self::$content;
		}

		/**
		 * @deprecated
		**/
		public static function format() {
			pathinfo(self::$path, PATHINFO_EXTENSION)
		}

		/**
		 * inherit methods
		**/
		public static function language() {}
	}
