<?php
    
    /**
     * This is the preload file. 
     * It allow you to do anything you want before calling templates
    **/
    
    /**
     * Data for template dev
    **/
    Response::assign(array(
        'config' => array(
            'urls' => array(
                'template' => path(PATH_TEMPLATES)
            )
        ),
        'try' => array('test'=> 'my variable value'),
        'array' => array('a','b', 'c', '...'),
        'title' => 'Pagetitle',
    ), true);
    
?>