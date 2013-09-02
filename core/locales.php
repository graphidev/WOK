<?php

    class Locales extends Session {
        
        private static $locales = array();
        
        public function __construct() {}

        
        /**
         * Get a locale file
        **/
        private static function load($locale) {
            if(file_exists(root(PATH_LOCALES.'/'.parent::$language."/$locale.json")))
                self::$locales[parent::$language][$locale] = json_decode(file_get_contents(root(PATH_LOCALES.'/'.parent::$language."/$locale.json")), true);
            
            elseif(file_exists(root(PATH_LOCALES.'/'.parent::$language."/$locale.properties")))
                self::generate($locale);
            
            else
                self::$locales[parent::$language][$locale] = array();
        }
        
        /**
         * Generate JSON locale
        **/
        private static function generate($locale) {
            $handle = fopen(root(PATH_LOCALES.'/'.parent::$language."/$locale.properties"), 'r');
            if($handle):
                while(!feof($handle)):
                    $line = fgets($handle);
                    if(substr($line, 0, 1) != '#'):
                        $path = str_replace('.', "']['", strstr(trim(addslashes($line)), '=', true));
                        $value = trim(addslashes(str_replace('=', '', strstr($line, '=', false))));
                                    
                        $data = array();
                        eval("\$data['$path']='$value';");
            
                        $origin = (isset(self::$locales[self::$language][$locale]) ? self::$locales[self::$language][$locale] : array());
                        self::$locales[self::$language][$locale] = array_merge_recursive($origin, $data);
                    endif;
                endwhile;
                            
                fclose($handle);
            endif;
            
            $json = fopen(root(PATH_LOCALES.'/'.parent::$language."/$locale.json"), 'w+');
            fwrite($json, json_encode(self::$locales[self::$language][$locale]));
            fclose($json);
        }
        
        /**
         * Get a locale translation
        **/
        public static function _e($path, $data = array()) {
            setLocale(LC_ALL, parent::$language);
            $locale = strstr($path, ':', true);
            
            if(empty($locale)): // locale by default
                $locale = 'default';
                $nodes = explode('.', $path);
            else:
                $nodes = explode('.', str_replace(':', '', strstr($path, ':')));
            endif;
            
            // Load locale if it isn't.
            if(!isset(self::$locales[parent::$language][$locale]))
                self::load($locale);
                    
            // Return the finale node value
            $translation = self::$locales[parent::$language][$locale];

            foreach($nodes as $i => $node) {
                if(is_array($translation) && isset($translation[$node])):
                    $translation = $translation[$node];
                else: // Try to get the default locale
                    if(parent::$language != SYSTEM_DEFAULT_LANGUAGE):
                        $backup = parent::$language;
                        parent::$language = SYSTEM_DEFAULT_LANGUAGE;
                        $translation = self::_e($path);
                        parent::$language = $backup;
                    else:
                        return $path;
                    endif;
                endif;
                
            }
            
            
            /**
             * default :input
             * string (:index|format)
             * date/time [:index|format]
             * money {:index|format}
             * reference &[locale:node.get.name]
             * reference &[~:node.get.name]
             * resume (:index|1234)
            **/
            if(!empty($data)):
                foreach($data as $index => $value) {
                                                        
                    // Replace with a date format
                    $translation = preg_replace_callback("#\[:$index\|(.+)\]#isU", function($matches) use (&$value){
                        $datetime = new DateTime($value);
                        return $datetime->format($matches[1]);
                    }, $translation);
                    
                    // Replace with a money format
                    $translation = preg_replace_callback("#\{:$index\|(.+)\}#isU", function($matches) use (&$value){
                        return money_format($matches[1], $value);
                    }, $translation);
                    
                    // Call a predefined format
                    $translation = preg_replace_callback("#\(:$index\|(.+)\)#isU", function($matches) use (&$value){
                        switch ($matches[1]) {
                            case 'trim':
                                return trim($value);
                            break;
                            case 'uppercase':
                                return mb_strtoupper($value, 'UTF-8');
                            break;
                            case 'lowercase':
                                return mb_strtolower($value, 'UTF-8');
                            break;
                            case 'reverse':
                                return \Compatibility\strrev($value);
                            default:
                                if(is_numeric($matches[1]))
                                    return resume($value, intval($matches[1]));
                                else
                                    return $value;
                        }
                    }, $translation);
                    
                    // Replace with locale money format
                    $translation = str_replace("{:$index}", money_format('%i', floatval($value)), $translation);
                    
                    // Replace with the value
                    $translation = str_replace(":$index", $value, $translation);
                    
                }
            endif;
            
            // Replace with an other translation
            $language = parent::$language; // $this allowed in anonymous functions with PHP 5.4 and newer
            $translation = preg_replace_callback("#&\[(.+)\]#isU", function($matches) use (&$language, &$locale){
                $locales = new Locales($language);
                return $locales->_e(str_replace('~', $locale, $matches[1]));
            }, $translation);
            
            return $translation;
                                
        }
        
    }

?>