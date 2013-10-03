<?php
    
    /**
     * This is the preload file. 
     * It allow you to do anything you want before calling templates
    **/ 
    
    /**
     * Define global template functions
     * Call them as $index($parameters, ...);
     * eg: $zone('inc/headers');
    **/
    $zone = function($path) {
        $zone = root(PATH_TEMPLATES."/$path.php");
        if(file_exists($zone))
            include($zone);
        else
            Console::log("Can't call zone '$zone'", Console::LOG_TEMPLATE);
    };
    Response::assign(array(
        'zone' => $zone,
        'headers' => function() use($zone) {
            $zone('inc/headers');
        },
        'footer' => function() use($zone) {
            $zone('inc/footer');
        },
        'sidebar' => function() use($zone) {
            $zone('inc/sidebar');
        }
    ));

    
    /**
     * Define a global view parser
     * This is the bad template engine method : PHP is still a template engine, prefer assign some functions !
    **/
    $parser = function($buffer, $data) {
        $tpl = new Template($buffer);  
        return $tpl->parse($data);
    };
    

    /**
     * Try it on homepage
    **/
    Controller::assign(true, function() use($parser) {
        
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
        Response::view('homepage', 200, $parser);        
        
    },true);
    
?>