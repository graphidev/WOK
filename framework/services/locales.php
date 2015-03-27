<?php

    namespace Framework\Services;

    class Locales {

        /**
         * @var array       Locales collection
        **/
        private $locales     = array();

        /**
         * @var string      Language code
        **/
        private $language;

        /**
         * @const Relative locales path
        **/
        const PATH_LOCALES            = '/locales';


        /**
         * Initialize locales parameters
         * @param string    $language       Language code
        **/
        public function __construct($language) {
            $this->language = $language;
            setLocale(LC_ALL, $language.'.UTF-8');
        }


        public function translate($path, array $values = array()) {

            $locale = $this->_get($path);
            if(!$locale) $locale = $this->_load($path);

            // translate locale ...

            return $locale;
        }


        private function _get($path) {
            if(!isset( self::$locales[$path = $this->base.'/'.$path] ))
                return false;

            $locale = $this->_load(strstr($path, ':', true));

            return self::$locales[$path = $this->base.'/'.$path];
        }


        private function _load($file) {

            $path = root(self::PATH_LOCALES.'/'.$file.'.properties');

            if(!file_exists($path))
                return false;

            $data = file_get_contents($file);


            return $locale;

        }


    }
