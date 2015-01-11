<?php
    
    /**
     * Manage and return locales
     *
     * @package Core
    **/
    class Locales {
        
        private static $locales     = array();        
        
        /**
         * Load a locale file
         * @param string    $locale
        **/
        private static function _load($locale, $language) { 
            $source = root(PATH_LOCALES.'/'.$language."/$locale.properties");
            $parsed = root(PATH_TMP.'/'.str_replace('/', '.', $language.".$locale.json"));
            if(file_exists($parsed)):
                if(file_exists($source) && filemtime($source) > filemtime($parsed)):
                    self::_generate($locale, $language);
                else:
                    self::$locales[$language][$locale] = json_decode(file_get_contents($parsed), true);
                endif;
            
            elseif(file_exists($source)):
                self::_generate($locale, $language);
            
            else:
                self::$locales[$language][$locale] = array();
            endif;
        }
        
        
        /**
         * Generate JSON locale
         * @param string    $locale
        **/
        private static function _generate($locale, $language) {
            $handle = fopen(root(PATH_LOCALES.'/'.$language."/$locale.properties"), 'r');
            if($handle):
                while(!feof($handle)):
                    $line = trim(fgets($handle));
                    $beginwith = substr($line, 0, 1);
                    if($beginwith != '#' && $beginwith != '!'):
                        $path = trim(str_replace('.', "']['", strstr(trim(addslashes($line)), '=', true))); // Property name
                        $value = trim(addslashes(str_replace('=', '', strstr($line, '=', false)))); // Property value
                        $value = str_replace(array('\\\\r\\\\n', '\\\\r', '\\\\n'), PHP_EOL, $value); // Allow breaklines in value           		
			
                        $data = array();
                        eval("\$data['$path']='$value';");
						            
                        $origin = (isset(self::$locales[$language][$locale]) ? self::$locales[$language][$locale] : array());
                        self::$locales[$language][$locale] = array_merge_recursive($origin, $data);
                    endif;
                endwhile;
                            
                fclose($handle);
            endif;
            
            $tmp = str_replace('/', '.', $locale);
            $json = fopen(root(PATH_TMP.'/'.$language.".$tmp.json"), 'w+');
            fwrite($json, json_encode(self::$locales[$language][$locale]));
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
        public static function _e($path, $data = array(), $language) {                        
            setLocale(LC_ALL, $language.'.UTF-8');
            $locale = strstr($path, ':', true);
            
            if(empty($locale)): // locale by default
                $locale = 'default';
                $nodes = $path;
            else:
                $nodes = str_replace(':', '', strstr($path, ':'));
            endif;
            
            // Load locale if it isn't.
            if(!isset(self::$locales[$language][$locale]))
                self::_load($locale, $language);
                                
            // Return the finale node value
            if(isset(self::$locales[$language][$locale]))
                $translation = self::$locales[$language][$locale];
			
            else
                $translation = null;

			$translation = array_value($nodes, $translation, false);
						
			if(is_array($translation) || $translation === false) { // Invalid node value
				trigger_error("Locales '$path'($language) not found", E_USER_NOTICE);
				
				if($language != SYSTEM_DEFAULT_LANGUAGE)
					$translation = self::_e($path, $data, SYSTEM_DEFAULT_LANGUAGE);
				
				else
					return $path;
			}
            
            if(!empty($data)) { 
				
                foreach($data as $index => $value) {
										
					/**
					 * Date format
					 * @syntax &[:data|format] or &[:data]
					**/
                    $translation = preg_replace_callback('#&\[:'.preg_quote($index).'\|(.+)\]#isU', function($matches) use (&$value){
                        return strftime($matches[1], strtotime($value));
                    }, $translation);
					
                    $translation = str_replace("&[:$index]", strftime('%c', strtotime($value)), $translation);
                    
					/**
					 * Money format
					 * @syntax $[:data|format] or $[:data]
					**/
                    $translation = preg_replace_callback('#\$\[:'.preg_quote($index).'\|(.+)\]#isU', function($matches) use (&$value){
                        return money_format($matches[1], $value);
                    }, $translation);

                    $translation = str_replace("$[:$index]", money_format('%i', floatval($value)), $translation);
					
					/**
					 * Custom data format 
					 * @syntax %[:data|format]
					**/
                    $translation = preg_replace_callback('#\%\[:'.preg_quote($index).'\|(.+)\]#isU', function($matches) use (&$value){
                        return sprintf($matches[1], $value);
                    }, $translation);
					
                    // Simple value replacement (:data or [:data])
                    $translation = str_replace(array("[:$index]", ":$index"), $value, $translation);
                    
                }
			}
			
            // Replace by an other translation @[path]
            $translation = preg_replace_callback("#\@\[(.+)\]#isU", function($matches) use (&$locale, $language){
				return Locales::_e(str_replace('~', $locale, $matches[1]), array(), $language);
            }, $translation);
            
            return entitieschars($translation);
                                
        }
        
    }

?>