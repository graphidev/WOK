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
     * Define methods to parse templates.
     * All it's methods depends of the render() method.
     * This class can be used both as core than in views.
     *
     * This class may also contains some shortcuts such as "zone"
     *
    **/
    class Engine {

        /**
         * @var $path                   Current template's folder path
        **/
        private $basepath;


        /**
         * @var $extension               Template files extension
        **/
        protected $extension       = null;


        /**
         * @var $helpers                 Custom template helpers collection
        **/
        protected $helpers         = array();


        /**
         * Instanciate view
         * @param string     $path             Templates path
         * @param string     $extension        Templates extension
        **/
        public function __construct($path, $extension = 'php') {

            $this->basepath  = $path;
            $this->extension = $extension;

        }

        /**
         * Assign engin custom helpers
         * @param   string          $name            Helper name
         * @param   Closure         $helper          Helper's function
        */
        public function addHelper($name, \Closure $helper) {
            $this->helpers[$name] = $helper;
        }


        /**
         * Render the view as a string
         * @param     string        $template         Template name
         * @param     array         $data             Data to transfert to the template
        **/
        public function render($template, array $data = array()) {

            // Template helper access
            $view = new Template($this->basepath.'/'.$template.'.'.$this->extension, $data, $this->helpers);

            // Override reserved constants
            $data['view'] = $view;

            // Generate and output view (prevent usage of $this)
            return call_user_func(function($view, $data) {

                ob_start();


                    extract($data);
                    /**
                     * because $view is also part of $data array,
                     * $view->__toString()  is called.
                    **/
                    include strval($view);

                    $buffer = ob_get_contents();

                ob_end_clean();

                return $buffer;

            },$view, $data);

        }


    }
