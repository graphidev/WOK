<?php

    namespace Controllers;

    class CMS {
        
        public function __construct() {
            //echo 'this have been called';   
        }    
        
        public function index() {
            
            return \Response::view('cms-home.php', 200);
            
        }
        
        public function page($name, $extension = null) {
            
            try {
                
                $page = new \Models\Page($name);
                
                $template = $page->getMeta('template');
                if(is_null($template)) $template = 'page';
                
                
                if($extension) return \Response::text($page->getContent());
                else return \Response::html(\Parsedown::instance()->text($page->getContent()));
                
            }
            catch(Exception $e) {
                throw $e;   
            }
                        
        }
        
        
    }