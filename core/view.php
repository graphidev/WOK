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
         * @return  boolean         True if the template file have been called, false otherwise
        **/
        public static function parse($template, array $data = array()) {
            if(!file_exists($file = root(PATH_TEMPLATES."/$template.php"))) {
                trigger_error("Template $template not found in ".PATH_TEMPLATES, E_USER_ERROR);
                return false;
            }
            
            extract(entitieschars($data));
            include $file;
            
            return true;
        }
        
        /**
         * This is the same as parse() method.
         * However it should be used instead of this previous one in template files : 
         * it can be updated specifically for this usage.
        **/
        public static function zone($path, array $data = array()) {
            return self::parse($path, $data);        
        }
 
    }

?>