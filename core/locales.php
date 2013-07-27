<?php

    class Locales {
        
        private $language;
        private $locales = array();
        
        
        public function __construct($language) {
            $this->language = $language;
            $this->load('default');
        }
        
        public function load($locale) {
            if(file_exists(root(PATH_LOCALES.'/'.$this->language."/$locale.json")))
                $this->locales[$locale] = json_decode(file_get_contents(root(PATH_LOCALES.'/'.$this->language."/$locale.json")), true);
            else
                $this->locales[$locale] = array();
        }
        
        // $locale->t('default:navigation.home')
        public function t($path, $data = array()) {
            $locale = strstr_before($path, ':');
            
            if(empty($locale)): // locale by default
                $locale = 'default';
                $nodes = explode('.', $path);
            else:
                $nodes = explode('.', str_replace(':', '', strstr($path, ':')));
            endif;
            
            // Load locale if it isn't.
            if(!isset($this->locales[$locale]))
                $this->load($locale);
            
            // Return the finale node value
            $locale = $this->locales[$locale];
            foreach($nodes as $i => $node) {
                if(is_array($locale) && isset($locale[$node]))
                    $locale = $locale[$node];
                else
                    return $path;
            }
            
            foreach($data as $index => $value) {
                $locale = str_replace(":$index", $value, $locale);
            }
            
            return $locale;
                                
        }
        
    }

?>