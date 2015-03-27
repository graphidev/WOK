<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Framework\Services;


    /**
     * The Autoloader class is made to set
     * custom classes autoloading pathes.
    **/
    class Loader {

        /**
         * @const PATH_PACKAGES         External packages directory's path
        **/
        const PATH_PACKAGES    = '/packages';


        /**
         * @var  array  $namespaces     Namespaces' pathes
        **/
        private $namespaces     = array();


        /**
         * @var  array  $classes        Classes' pathes
        **/
        private $classes        = array();


        /**
         * Instanciate the classes autoloading
        **/
        public function __construct($fn = 'Fn') {

            $self = $this;
            spl_autoload_register(function($class) use($self) {

                // Full autoload path
                if(!empty( $self->classes[$class] )
                    && file_exists($file = SYSTEM_ROOT . $self->classes[$class])) {
                    require_once $file;
                    return;
                }

                // Search for an existing path
                foreach($self->namespaces as $namespace => $path) {

                    $length = strlen($namespace);

                    if(substr($class, 0, $length) != $namespace)
                        continue;

                    $classname = substr($class, $length+1); // Remove namespace path
                    $filepath = strtolower(str_replace( array('\\', '/'), DIRECTORY_SEPARATOR, $classname ));

                    if(file_exists($file = SYSTEM_ROOT . $path.'/'.$filepath.'.php')) {
                        require_once $file;
                        return;
                    }

                }

            });

            // Initialize the custom autoloader
            if($fn) \class_alias(__CLASS__, $fn);

        }


        /**
         * Load a defined library
         * @param string $library   The library name. Library files must have the following syntax : [name].library.php
        **/
        public function load($library) {

            if(!file_exists($path = root(PATH_PACKAGES .'/'. $library . '.library.php')))
                trigger_error('Call to undefined library '. $library .' within '. PATH_LIBRARIES, E_USER_ERROR);

            require_once $path;
        }


        /**
         * Set a class path
         * @param   string     $name       Absolute class' name
         * @param   string     $file       Class' file path
        **/
        public function setClass($name, $file) {
            $this->classes[$name] = $file;
        }

        /**
         * Set a namespace path
         * @param   string     $namespace       Namespace name (base or complete)
         * @param   string     $file            Associated namespace's path
        **/
        public function setNamespace($namespace, $path) {
            $this->namespaces[$namespace] = $path;
        }

        /**
         * Call of a function : try to load function with a defined file name
         * This method usage should be used exceptional in order to keep system performance
         * @param string $function The function that is called
         * @param array  $arguments The arguments passed to the function
        **/
        public static function __callStatic($function, $arguments) {

            if(!function_exists($function) && file_exists($path = root(PATH_PACKAGES .'/'. $function . '.function.php'))) {
                require_once $path;
            }

            if(!function_exists($function))
                trigger_error('Call to undefined function '.$function.'()', E_USER_ERROR);

            return call_user_func_array($function, $arguments);

        }

    }
