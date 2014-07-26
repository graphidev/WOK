<?php

    /**
     * Define methods to parse templates.
     * All it's methods depends of the parse() method.
     * This class can be used both as core than in views.
     * 
     * This class may also contains some shortcuts such as "zone"
     *
     * @package Templates
    **/
    
    class View {
        
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
        public static function parse($template, array $data = array(), $entities = true) {
            if(!file_exists($file = root(PATH_TEMPLATES."/$template.php"))) {
                trigger_error("Template $template not found in ".PATH_TEMPLATES, E_USER_ERROR);
                return false;
            }
            
            extract($entities ? entitieschars($data) : $data);
            include $file;
            
            return true;
        }
        
        /**
         * This is the same as parse() method.
         * However it should be used instead of this previous one in template files : 
         * This method define a local path from where it is called 
         * except if the root "/" is setted
        **/
        public static function zone($path, array $data = array()) {
                        
            if(substr($path, 0, 2) == './' || substr($path, 0, 1) != '/') {
                $backtrace = debug_backtrace();
                $root = root(PATH_TEMPLATES);
                
                if(substr($path, 0, 2) == './') $path = substr($path, 2);
                $path = substr(dirname($backtrace[0]['file']), strlen($root)+1)."/$path";
            }
            
            return self::parse($path, $data);        
        }
 
    }

?>