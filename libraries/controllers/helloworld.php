<?php

    namespace Controllers;
        
    class HelloWorld {
        
        public function homepage() {
            return array(
                'title' => 'Hello, World !',
                'content' => \Tools\Markdown::defaultTransform(file_get_contents(root('/README.md')))
            );
        }
        
        public function data() {
            return array(
                'data' => array(
                    'array' => array('a', 'b', 'c', '...'),
                    'string' => 'abcd...',
                )
            );
        }
        
    }

?>