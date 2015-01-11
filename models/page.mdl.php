<?php

    namespace Models;

    class Page {
        
        private $type;
        private $content;
        private $meta = array(
            'title'       	=> 'Title',
            'description' 	=> 'Description',
            'author' 		=> 'Author',
            'date' 			=> 'Date',
            'robots'     	=> 'Robots',
            'template'      => 'Template'
        );
     
        public function __construct($page) {            
            if(file_exists($file = root(PATH_FILES."/content/$page.md"))) {
                $this->type = 'page';
                $this->parse($file);
            }
            elseif(file_exists($file = root(PATH_FILES.'/content/'.substr($page, 0, -1).'/index.md'))) {
                $this->type = 'index';
                $this->parse($file);
            }
            else {
                throw new \Exception('Page not found', 404);   
            }
        }
        
        private function parse($file) {
            
            $content = file_get_contents($file);

            foreach ($this->meta as $name => $regex){
                if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $content, $match) && $match[1]){
                    $this->meta[$name] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
                } else {
                    $this->meta[$name] = null;
                }
            }
		                  
            $this->content = preg_replace('#/\*.+?\*/#s', '', $content); // Remove comments and meta
        }
        
        
        public function getMeta($name = null) {
            if(!empty($name) && isset($this->meta[$name]))
                return $this->meta[$name];
            
            elseif(empty($name))
                return $this->meta;
            
            else
                return null;
        }
        
        public function getContent() {
            return $this->content;   
        }
        
        public function getType() {
            return $this->type;
        }
        
    }

?>