<?php
    
    /**
     * Manage and return locales
     *
     * @package Core
    **/
    class Locales {
        
        private static $language = null;
        private static $locales = array();
        
        
        /**
         * Load a locale file
         * @param string    $locale
        **/
        private static function _load($locale) { 
            $source = root(PATH_LOCALES.'/'.self::$language."/$locale.properties");
            $parsed = root(PATH_TMP.'/'.str_replace('/', '.', self::$language.".$locale.json"));
            if(file_exists($parsed)):
                if(file_exists($source) && filemtime($source) > filemtime($parsed)):
                    self::_generate($locale);
                else:
                    self::$locales[self::$language][$locale] = json_decode(file_get_contents($parsed), true);
                endif;
            
            elseif(file_exists($source)):
                self::_generate($locale);
            
            else:
                self::$locales[self::$language][$locale] = array();
            endif;
        }
        
        
        /**
         * Generate JSON locale
         * @param string    $locale
        **/
        private static function _generate($locale) {
            $handle = fopen(root(PATH_LOCALES.'/'.self::$language."/$locale.properties"), 'r');
            if($handle):
                while(!feof($handle)):
                    $line = trim(fgets($handle));
                    $beginwith = substr($line, 0, 1);
                    if($beginwith != '#' && $beginwith != '!'):
                        $path = trim(str_replace('.', "']['", strstr(trim(addslashes($line)), '=', true))); // Property name
                        $value = trim(addslashes(str_replace('=', '', strstr($line, '=', false)))); // Property value
                        $value = str_replace(array('\\\\r\\\\n', '\\\\r', '\\\\n'), PHP_EOL, $value); // Allow breaklines in value           
                        $value = htmlentities($value);
            
                        $data = array();
                        eval("\$data['$path']='$value';");
            
                        $origin = (isset(self::$locales[self::$language][$locale]) ? self::$locales[self::$language][$locale] : array());
                        self::$locales[self::$language][$locale] = array_merge_recursive($origin, $data);
                    endif;
                endwhile;
                            
                fclose($handle);
            endif;
            
            $tmp = str_replace('/', '.', $locale);
            $json = fopen(root(PATH_TMP.'/'.self::$language.".$tmp.json"), 'w+');
            fwrite($json, json_encode(self::$locales[self::$language][$locale]));
            fclose($json);
        }
        
        
        /**
         * Get a locale translation
         * @exemple Locales:_e('locale:path.to.the.translation', array('param'=>'value', ...))
         *
         * @param string    $path
         * @param array     $data
         * @param string    $language
        **/
        public static function _e($path, $data = array(), $language = null) {
            if(empty($language)):
                if(empty(self::$language))
                    self::$language = (\Session::get('language') !== false ? \Session::get('language') : SYSTEM_DEFAULT_LANGUAGE);
            
                $language = self::$language;
            endif;
                        
            setLocale(LC_ALL, $language.'.UTF-8');
            $locale = strstr($path, ':', true);
            
            if(empty($locale)): // locale by default
                $locale = 'default';
                $nodes = explode('.', $path);
            else:
                $nodes = explode('.', str_replace(':', '', strstr($path, ':')));
            endif;
            
            // Load locale if it isn't.
            if(!isset(self::$locales[$language][$locale]))
                self::_load($locale);
                                
            // Return the finale node value
            if(isset(self::$locales[$language][$locale]))
                $translation = self::$locales[$language][$locale];
            else
                $translation = null;

            foreach($nodes as $i => $node) {
                if(is_array($translation) && isset($translation[$node])):
                    $translation = $translation[$node];
                else: // Try to get the default locale
                    trigger_error("Locales '$path'($language) not found", E_USER_NOTICE);
                    if($language != SYSTEM_DEFAULT_LANGUAGE):
                        $translation = self::_e($path, $data, SYSTEM_DEFAULT_LANGUAGE);
                    else:
                        return $path;
                    endif;
                endif;
                
            }
            
            
            /**
             * default      :input
             * resume       (:index|length)
             * date/time    [:index|format] [:index]
             * money        {:index|format} {:index}
             * reference    &[locale:node.get.name]
             * reference    &[~:node.get.name]
            **/
            if(!empty($data)):
                foreach($data as $index => $value) {
                    
                    // Replace with a date format [advanced]
                    $translation = preg_replace_callback("#\[:$index\|(.+)\]#isU", function($matches) use (&$value){
                        return strftime($matches[1], strtotime($value));
                    }, $translation);
                    
                    // Replace with a money format [advanced]
                    $translation = preg_replace_callback("#\{:$index\|(.+)\}#isU", function($matches) use (&$value){
                        return money_format($matches[1], $value);
                    }, $translation);
                    
                    // Limit the data length (words number)
                    $translation = preg_replace_callback("#\(:$index\|[0-9]+\)#isU", function($matches) use (&$value){
                        return resume($value, intval($matches[1]));                        
                    }, $translation);
                    
                    // Replace with a date format [basic]
                    $translation = str_replace("[:$index]", strftime('%c', strtotime($value)), $translation);
                    
                    // Replace with locale money format [basic]
                    $translation = str_replace("{:$index}", money_format('%i', floatval($value)), $translation);
                    
                    // Replace with the value
                    $translation = str_replace(":$index", $value, $translation);
                    
                }
            endif;
            
            // Replace with an other translation
            $translation = preg_replace_callback("#&\[(.+)\]#isU", function($matches) use (&$locale){
                return Locales::_e(str_replace('~', $locale, $matches[1]));
            }, $translation);
            
            
            
            return $translation;
                                
        }
        
    }

?>