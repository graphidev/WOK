<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Locales;

    /**
     * The Locales class provides
     * an interace to access and
     * parse translations messages.
    **/
    class Locales {

        /**
         * @const   DEFAULT_NAMESPACE      Default messages namespace
        **/
        const DEFAULT_NAMESPACE    = 'messages';

        /**
         * @var $locale            Translations locale
        **/
        protected $locale;

        /**
         * @var $path               Translations files path
        **/
        protected $path;

        /**
         * @var $translations       Translations collection
        **/
        protected $messages         = array();


        /**
         * Instanciate locales interface
         * @param string    $path       Translations files path
         * @param string    $locale     Locale code
        **/
        public function __construct($path, $locale) {

            $this->locale   = $locale;
            $this->path     = $path.'/'.$locale;

            setLocale(LC_ALL, $locale.'.UTF-8', $locale);

            // Default parser : number
            $this->setParser('number', function($number) {
                $format = localeconv();
                return number_format($number, 0, $format['decimal_point'], $format['thousands_sep']);
            });

            // Default parser : date
            $this->setParser('date', function($time, $format = '%c') {
                return strftime($format, $time);
            });

            // Default parser : money
            $this->setParser('money', function($money, $format = '%i') {
                return money_format($format, $money);
            });

            // Default parser : count
            $this->setParser('multi', function($number, $none, $single, $plural) {
                if($number == 0 && !empty($none))
                    return $none;

                // Use number locale format
                $format = localeconv();
                $number =  number_format($number, 0, $format['decimal_point'], $format['thousands_sep']);

                return sprintf( ($number > 1 ? $plural : $single), $number );
            });

        }


        /**
         * Add a custom locale parser
         * @param   string          $name           Parser name
         * @param   Closure         $callback       Parser function
        **/
        public function setParser($name, \Closure $callback) {
            $this->parsers[$name] = $callback;
        }



        /**
         * Get a specified message
         * @param   string      $namespace          Namespace string
         * @param   string      $key                Message key
        **/
        protected function _getMessage($namespace, $key) {

            if(!isset($this->messages[$namespace])) {

                $this->translations[$namespace] = new Messages(array());

                if(is_readable($filepath = $this->path.'/'.$namespace.'.ini'))
                    $this->translations[$namespace] = new Messages(parse_ini_file($filepath));

            }

            return $this->translations[$namespace]->getMessage($key);

        }




        /**
         * Get a parsed translation message
         * @param     string      $messsage           Message key
         * @param     array       $data               Data to set in the message
         * @param     string      $namespace          Message namespace
        **/
        public function translate($message, array $data = array(), $namespace = self::DEFAULT_NAMESPACE) {

            $translation = $this->_getMessage($namespace);

            if(!$translation)
                throw new \OutOfBoundsException('Undefined message "'.$key.'" in "'.$namespace.'" ('.$this->locale.')');

            // Reference variables : &{namespace->message}
            $translation = preg_replace_callback('#&\{(?<message>[a-z0-9_\.\-]+)\}#isU', function($m) use ($namespace) {

                $ref_separator  = '->';
                $ref_position   = mb_strpos($ref_separator, $m['message']);
                if($ref_position > 0) {

                    $ref_namespace  = mb_substr($m['message'], 0, $position);
                    $ref_message    = mb_substr($m['message'], $position + mb_strlen($ref_separator));

                    return $this->translate($ref_message, null, $ref_namespace);
                }

                return $this->translate($m['message'], null, $namespace);

            }, $translation);

            // Apply helpers : [helper:arg1|arg2|...]
            $translation = preg_replace_callback('#\[(?<helper>[a-z0-9_\-]+):(?<values>.+)\]#isU', function($m) {

                if(!isset($this->parsers[$m['helper']]))
                    return $m[0];

                return call_user_func_array($this->parsers[ $m['helper'] ], explode('|', $m['values']));

            }, $translation);

            // Data variables : ${var}
            if(is_array($data) && !empty($data)) {
                foreach($data as $key => $value) {
                    $translation = mb_str_replace('${'.$key.'}', $value, $translation);
                }
            }

            return $message;

        }


    }
