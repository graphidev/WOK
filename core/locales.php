<?php

    class Locales {
        
        private $language;
        private $locales = array();
        private static $accepted_languages = array();
        
        
        public function __construct($language) {
            self::$accepted_languages =  explode(',', SYSTEM_ACCEPT_LANGUAGES);
            $this->language = $language;
            $this->load('default');
        }
        
        
        private function load($locale) {
            if(file_exists(root(PATH_LOCALES.'/'.$this->language."/$locale.json")))
                $this->locales[$this->language][$locale] = json_decode(file_get_contents(root(PATH_LOCALES.'/'.$this->language."/$locale.json")), true);
            else
                $this->locales[$this->language][$locale] = array();
        }        
        
        public function _e($path, $data = array()) {
            setLocale(LC_ALL, $this->language);
            $locale = strstr($path, ':', true);
            
            if(empty($locale)): // locale by default
                $locale = 'default';
                $nodes = explode('.', $path);
            else:
                $nodes = explode('.', str_replace(':', '', strstr($path, ':')));
            endif;
            
            // Load locale if it isn't.
            if(!isset($this->locales[$this->language][$locale]))
                $this->load($locale);
            
            // Return the finale node value
            $translation = $this->locales[$this->language][$locale];
            foreach($nodes as $i => $node) {
                if(is_array($translation) && isset($translation[$node])):
                    $translation = $translation[$node];
                else: // Try to get the default locale
                    if($this->language != self::$accepted_languages[0]):
                        $backup = $this->language;
                        $this->language = self::$accepted_languages[0];
                        $translation = $this->_e($path);
                        $this->language = $backup;
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
            $language = $this->language; // $this allowed in anonymous functions with PHP 5.4 and newer
            $translation = preg_replace_callback("#&\[(.+)\]#isU", function($matches) use (&$language, &$locale){
                $locales = new Locales($language);
                return $locales->_e(str_replace('~', $locale, $matches[1]));
            }, $translation);
            
            return $translation;
                                
        }
        
    }

?>