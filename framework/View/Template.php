<?php

    /**
     * Web Operational Kit
     * The neither huge no micro extensible framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace View;

    /**
     * The Template class provides
     * a helper object to generate a view
    **/
    class Template {


        protected $filepath;

        /**
         * @var $helpers                 Custom template helpers collection
        **/
        protected $data     = array();

        /**
         * @var $helpers                 Custom template helpers collection
        **/
        protected $helpers  = array();


        /**
         * Instanciate template helpers
         * @param  string       $filepath           Current template path
         * @param  array        $data               Current view data
         * @param  array        $helpers            additionnal helpers
        **/
        public function __construct($filepath, array $data = null, array $helpers = array()) {

            $this->filepath     = $filepath;
            $this->data         = $data;
            $this->helpers      = $helpers;

        }


        /**
         * Escape characters
         * @param string     $data        Source string to escape
        **/
        public function escape($data) {
            return htmlentities($data, ENT_QUOTES | ENT_HTML5);
        }


        /**
         * Include an other template within the current one
         * @note This method uses the main render method
         * @param string    template        Relative zone path
         * @param array     $data           Data to assign (as addition) in the zone
        **/
        public function zone($template, array $data = array()) {

            // Keep engine parameters
            $engine = new Engine(
                dirname($this->filepath),
                pathinfo($this->filepath, PATHINFO_EXTENSION)
            );

            // Keep custom helpers
            foreach($this->helpers as $name => $function) {
                $engine->addHelper($name, $function);
            }

            // Override data
            $data = (!empty($data) ? array_merge($this->data, $data) : $this->data);

            echo $engine->render($template, $data);

        }


        /**
         * Allow data properties access
         * @param string    $property       Data's key
        **/
        public function __get($property) {

            if(!isset($this->data[$property]))
                trigger_error('Undefined view property '.$property.' in '.$this->filepath, E_USER_ERROR);

            return $this->data[$property];

        }


        /**
         * Allow custom helpers call
         * @param string    $name           Helper name
         * @param string    $parameters     Helper's arguments
        **/
        public function __call($helper, $parameters) {
            if(!isset($this->helpers[$helper]))
                trigger_error('Undefined view helper '.$helper.' in '.$this->filepath, E_USER_ERROR);

            return call_user_func_array($this->helpers[$helper], $parameters);
        }


        /**
         * Access to the template path
        **/
        public function __toString() {
            return $this->filepath;
        }


    }
