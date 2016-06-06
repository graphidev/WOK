<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Application;


    /**
     * The Loader class provides
     * an interface to autoload classes
    **/
    class Loader {


        /**
         *@var $classes              Autoloading paths
        **/
        protected $classes           = array();


        /**
         *@var $namespaces           Autoloading paths
        **/
        protected $namespaces        = array();


        /**
         * Set default loading folder
         * @param   string      $basepath           Autoloading base path
        **/
        public function __construct() {

            spl_autoload_register(function($classname) {

                if(isset($this->classes[$classname])) {
                    $this->loadFile($this->classes[$classname]);
                }
                else {

                    foreach($this->namespaces as $prefix => $path) {

                        if(mb_substr($classname, 0, mb_strlen($prefix)) == $prefix) {

                            if(is_closure($path)) {
                                $path = call_user_func($path, $classname);
                            }
                            else {
                                $path .= mb_str_replace('\\', DIRECTORY_SEPARATOR, $classname);
                            }

                            $this->loadFile($path.'.php');

                            break;

                        }

                    }

                }

            });

        }

        /**
         * Register a class name loading path
         * @param   string      $class          Absolute class name path (with namespace)
         * @param   string      $path           Class associated file path
        **/
        public function addPath($class, $path) {
            $this->classes[$class] = $path;
        }


        /**
         * Register a prefixed namespace and it's loading path
         * @param   string      prefix          Namespace prefix matcher
         * @param   string      $path           Class associated file path
        **/
        public function addPrefix($prefix, $path) {
            $this->namespaces[$prefix] = $path;
        }


        /**
         * Load a file (without autoloading)
         * @param   string      $filepath           File path
        **/
        public function loadFile($filepath) {

            if(file_exists($filepath))
                return require $filepath;

        }


    }
