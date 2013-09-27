<?php

    class Locales extends Session {
        
        private static $locales = array();
        private static $formats = array();
        
        public function __construct() {}
        
        /**
         * Get a locale file
        **/
        private static function load($locale) {
            $source = root(PATH_LOCALES.'/'.parent::$language."/$locale.properties");
            $parsed = root(PATH_LOCALES.'/'.parent::$language."/$locale.json");
            if(file_exists($parsed)):
                if(file_exists($source) && filemtime($source) > filemtime($parsed)):
                    self::generate($locale);
                else:
                    self::$locales[parent::$language][$locale] = json_decode(file_get_contents($parsed), true);
                endif;
            
            elseif(file_exists($source)):
                self::generate($locale);
            
            else:
                self::$locales[parent::$language][$locale] = array();
            endif;
        }
        
        /**
         * Generate JSON locale
        **/
        private static function generate($locale) {
            $handle = fopen(root(PATH_LOCALES.'/'.parent::$language."/$locale.properties"), 'r');
            if($handle):
                while(!feof($handle)):
                    $line = trim(fgets($handle));
                    $beginwith = substr($line, 0, 1);
                    if($beginwith != '#' && $beginwith != '!'):
                        $path = trim(str_replace('.', "']['", strstr(trim(addslashes($line)), '=', true))); // Property name
                        $value = trim(addslashes(str_replace('=', '', strstr($line, '=', false)))); // Property value
                        $value = str_replace(array('\n','\r'), "\r\n", $value); // Allow breaklines in value
                                    
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
             * default      :input
             * string       (:index|format)
             * date/time    [:index|format]
             * money        {:index|format} {:index}
             * reference    &[locale:node.get.name]
             * reference    &[~:node.get.name]
             * resume       (:index|1234)
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
                        $formats = explode(',', $matches[1]);
                        foreach($formats as $i => $format) {
                            switch ($format) {
                                case 'trim':
                                    $value = trim($value);
                                break;
                                case 'uppercase':
                                    $value = mb_strtoupper($value, 'UTF-8');
                                break;
                                case 'lowercase':
                                    $value = mb_strtolower($value, 'UTF-8');
                                break;
                                case 'reverse':
                                    $value = \Compatibility\strrev($value);
                                default:
                                    if(is_numeric($format)):
                                        $value = resume($value, intval($format));
                                    elseif(!empty(self::$formats[$format])):
                                        $parser = self::$formats[$format];
                                        $value = $parser($value);
                                    else:
                                        return $value;
                                    endif;
                            }
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
                return Locales::_e(str_replace('~', $locale, $matches[1]));
            }, $translation);
            
            return $translation;
                                
        }
        
        
        /**
         * Set a format
        **/
        public static function assign($format, $callback) {
            self::$formats[$format] = $callback;
        }
        
    }

?>