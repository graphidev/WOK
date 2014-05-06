<?php

	/**
     * Initialize WOK
    **/
	require_once 'core/init.php';


    /**
     * Generate session and cookies requirements such as language
     * We supposed that these session and cookies values are not 
     * changed by custom developments (reserved keys)
    **/
    if(!Session::has('language') && Cookie::exists('language', true)):
        Session::set('language', Cookie::get('language'));

    else:
        
        $languages = get_accepted_languages(explode(' ', SYSTEM_LANGUAGES));
        
        if(!empty($languages))
            $language = array_shift($languages);
            
        else
            $language = SYSTEM_DEFAULT_LANGUAGE;
        
        Session::set('language', $language);

        if(!Cookie::exists('language'))
            Cookie::set('language', $language, 15811200);        

    endif;

    if(!Session::has('uniqid')):
        Session::set('uniqid', Cookie::exists('uniqid') ? Cookie::get('uniqid') : uniqid(sha1(time())));
        Cookie::set('uniqid', Session::get('uniqid'));
    endif;

    
    /**
     * Load XML manifest and initialize
     * Request class according to manifest
    **/
    Manifest::load();
    Request::init();


    /**
     * Set Custom things routes
     * This should be use for development. Prefere using 
     * XML manifest in order to keep framework structure
    **/
    if(file_exists(root(PATH_VAR.'/manifest.php')))
        require_once(root(PATH_VAR.'/manifest.php'));
    
    
    
    /**
     * Ongoing maintenance 
    **/
    Controller::route(SYSTEM_MAINTENANCE, function() {
        Response::cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, 'maintenance');
        Response::view('maintenance', 503);
    }, true);


    /**
     * Set static pages controller (special)
    **/
    Controller::route(Request::get('action') == 'static', function() {
        Response::cache(Response::CACHETIME_MEDIUM, Response::CACHE_PROTECTED, str_replace('/', '-', Request::uri()));
        Response::view(Request::uri(), 200);
    }, true);


    /**
     * Set manifest controllers
    **/
    Controller::route(Request::get('action') ? true : false, function() {
        list($controller, $action) = explode(':', strtolower(Request::get('action')));
        if(file_exists(root(PATH_CONTROLLERS."/$controller.ctrl.php"))):
            Controller::call($controller, $action);
        else:
            trigger_error("Controller '$controller' not found", E_USER_ERROR);
        endif;
    }, true);


    /**
     * Set default homepage controller
    **/
    Controller::route(Request::uri() == '' ? true : false, function() {
        Response::cache(Response::CACHETIME_MEDIUM, Response::CACHE_PROTECTED, 'homepage');
        Response::view('homepage', 200);
    }, true);


    /**
     * If there is no response for any controller
     * Just send a 404 response
    **/
    Controller::route(true, function() {
        Response::cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, '404');
        Response::view('404', 404);
    }, true);


    /**
     * Invoke the controller queue
    **/
    Controller::invoke();

    
    /**
     * Output response
    **/
    Response::output();

?>
