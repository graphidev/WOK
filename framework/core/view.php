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
     * Define methods to parse templates.
     * All it's methods depends of the parse() method.
     * This class can be used both as core than in views.
     *
     * This class may also contains some shortcuts such as "zone"
     *
    **/

    class View {

        /**
         * @const PATH_TEMPLATES       Templates' root directory path
        **/
        const PATH_TEMPLATES      = '/templates';

        /**
         * @var $path   Current view path
        **/
        private $path;

        /**
         * @var $data   Given data collection
        **/
        protected $data;



        //public function __construct($template, $root = true) {}


        /**
         * Shortcut to the non-static parse method
         * @see View::parse
        **/
        public static function display($template, array $data = array(), $root = true) {
            $view = new self;
            return $view->parse($template, $data, $root);
        }



        /**
         * Parse template file with PHP engine.
         * It also check template existence and generate
         * an user error if the file is not found
         *
         * @param   string      $template       Template file path
         * @param   array       $data           Data to parse in template (optional)
         * @param   boolean     $entities       Convert caracters to HTML entites
         * @return  boolean     True if the template file have been called, false otherwise
        **/
        public function parse($template, array $data = array(), $root = true) {

            if(!file_exists($file = root(self::PATH_TEMPLATES.'/'.$template.'.tpl.php')))
                trigger_error('Template "'.$template.'" not found in '.self::PATH_TEMPLATES, E_USER_ERROR);


            if($root)
                $this->path = substr( dirname($file), strlen(SYSTEM_ROOT.self::PATH_TEMPLATES) );

            $this->data = new \Framework\Utils\Collection( (array) $data );

            ob_start();

            include $file;

            $buffer = ob_get_contents();
            ob_end_clean();

            return $buffer;
        }


        /**
         * Include an other template within the current one
         * @note This method uses the main parse method
         * @param string    $zone       Relative zone path
         * @param array     $data       Data to parse in template (optional)
        **/
        public function zone($zone, array $data = null) {
            //echo $this->path.'/'.$zone;
            //echo root(self::PATH_TEMPLATES.''.$this->path.'/'.$zone);

            $path = $zone;

            if(!empty($this->path))
                $path =  $this->path . '/' . $path;

            echo self::display($path, (!empty($data) ? $data : (array) $this->data), false);
        }

        /**
         * Check whether a data exists or not
         * @param $property    string       Data's name
        **/
        public function __isset($property) {
            return (isset($this->data[$property]) && !is_null($this->data[$property]));
        }

        /**
         * Get data value
         * @param $property    string       Data's name
        **/
        public function __get($property) {

            if(!isset($this->data[$property]) && !is_null($this->data[$property]))
                trigger_error('Unable to get "'.$property.'" data in this view', E_USER_ERROR);

            return $this->data[$property];

        }

        /**
         * Get assets path
         * @param $property    string       Data's name
        **/
        public function path($asset) {
            return path(self::PATH_TEMPLATES.$this->path.$asset);
        }

    }

?>
