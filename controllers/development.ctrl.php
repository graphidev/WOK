<?php

    namespace Controllers;
    use \Response;

    class Development {
     
        public function index() {
            return Response::text(function() {
                return 'this controller is working fine';
            })->cache(5, Response::CACHE_PROTECTED, 'dev');
        }
        
    }