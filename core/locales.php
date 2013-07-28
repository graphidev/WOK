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
            $locale = strstr_before($path, ':');
            
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
            $locale = $this->locales[$this->language][$locale];
            foreach($nodes as $i => $node) {
                if(is_array($locale) && isset($locale[$node])):
                    $locale = $locale[$node];
                else: // Try to get the default locale
                    if($this->language != self::$accepted_languages[0]):
                        $backup = $this->language;
                        $this->language = self::$accepted_languages[0];
                        $default = $this->_e($path);
                        $this->language = $backup;
                        return $default;
                    else:
                        return $path;
                    endif;
                endif;
                
            }
            
            if(!empty($data)):
                foreach($data as $index => $value) {
                    $locale = str_replace(":$index", $value, $locale);
                }
            endif;
            
            return $locale;
                                
        }
        
    }

?>