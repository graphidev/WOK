<?php
    
    /**
     * This is the preload file. 
     * It allow you to do anything you want before calling templates
    **/    
    
    /**
     * Data for template dev
    **/
    Controller::assign(true, function() {
        
        Response::cache(Response::CACHETIME_MEDIUM, Response::CACHE_PROTECTED);
                
        $data = array(
            'config' => array(
                'urls' => array(
                    'template' => path(PATH_TEMPLATES)
                ),
                'zones' => array(
                    'headers' => 'headers'
                ),
            ),
            'session' => array(
                'is_logged' => true,
                   'account' => array(
                    'username' => 'Sebasalex',
                    'publicname' => 'Sébastien ALEXANDRE',
                    'firstname' => 'Sébastien',
                    'lastname' => 'ALEXANDRE',
                )
            ),
            'page' => array (
                'title' => 'Page title',
            ),
            'locales' => array(
                'test' => 'blabla'
            )
        );
            
        Response::assign($data);
        Response::view('homepage', 200);        
        
    },true);
    
?>