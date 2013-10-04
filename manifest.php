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
    $functions = array(
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
    );

    
    /**
     * Define a global view parser
     * This is the bad template engine method : PHP is still a template engine, prefer assign some functions !
    **/
    $parser = function($buffer, $data) {
        $tpl = new \Tools\Template($buffer);  
        return $tpl->parse($data);
    };
    

    /**
     * Try it on homepage
    **/
    Controller::assign(function($request) { return empty($request); }, function() use($functions) {
        
        Response::cache(Response::CACHETIME_MEDIUM, Response::CACHE_PROTECTED);
        
        $controller = new \Controllers\HelloWorld;  

        $data = array('page' => $controller->homepage());
        
        
        Response::assign($functions);   
        Response::assign($data);
        Response::view('homepage', 200, $parser);        
        
    },true);

    /**
     * Other pages
    **/
Controller::assign(true, function() use($functions, $parser) {
    $controller = new \Controllers\HelloWorld;  
    Response::assign($controller->data());
    Response::assign($functions);
    Response::view(Request::$URI, 200, $parser);
}, true);
    
?>