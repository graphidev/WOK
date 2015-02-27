<?php


	abstract class Entrypoint {

		protected static $language = SYSTEM_DEFAULT_LANGUAGE;
		private static $parameters = array();

		public function language($value = null) {

			if(!empty($value) && in_array($value, get_accepted_languages(explode(' ', SYSTEM_LANGUAGES))))
				self::$language = $value;

			return self::$language;
		}

	}
