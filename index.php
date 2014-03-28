<?php

	/**
     * Initialize WOK
    **/
	require_once 'core/init.php';


    /**
     * Generate session and cookies requirements such as language
     * We supposed that these session and cookies values are not 
     * changed by custom developments.
    **/
    if(!Session::has('language') && Cookie::exists('language', true)):

        Session::set('language', Cookie::get('language'));

    elseif(!Session::has('language')):
        
        $languages  = explode(',', str_replace('-', '_', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
        $accepted   = explode(' ', SYSTEM_LANGUAGES);

        foreach($languages as $i => $code) {
            
            if(in_array($code, $accepted)):
                $language = $code;
                break;
            endif;
            
        }

        if(!isset($language))
            $language = SYSTEM_DEFAULT_LANGUAGE;

        Session::set('language', $language);
        Cookie::set('language', $language);        

    endif;

    if(!Session::has('uniqid')):
        Session::set('uniqid', Cookie::exists('uniqid') ? Cookie::get('uniqid') : uniqid(sha1(time())));
        Cookie::set('uniqid', Session::get('uniqid'));
    endif;

    
    /**
     * Inititialize Required classes
    **/
    new App; // Initialize the app
    new Request; // Initialize request informations


    /**
     * Set Custom things
     * This should be use for development. Prefere using 
     * XML manifest in order to keep framework structure
    **/
    if(file_exists(root(PATH_VAR.'/manifest.php')))
        require_once(root(PATH_VAR.'/manifest.php'));

    
    /**
     * Ongoing maintenance 
    **/
    Controller::route(SYSTEM_MAINTENANCE, function() {
        Response::view('maintenance', 503, true);
    }, true);


    /**
     * Set static pages controller (special)
    **/
    Controller::route(Request::get('action') == 'static', function() {
        Response::view(Request::uri(), 200, true);
    }, true);


    /**
     * Set manifest controllers
    **/
    Controller::route(Request::get('action') ? true : false, function() {
        list($controller, $action) = explode(':', strtolower(Request::get('action')));
        if(file_exists(root(PATH_CONTROLLERS."/$controller.ctrl.php"))):
            Controller::call($controller, $action);
        else:
            trigger_error("Controller '$name' not found", E_USER_ERROR);
        endif;
    }, true);


    /**
     * Set default homepage controller
    **/
    Controller::route(Request::uri() == '' ? true : false, function() {
        Response::cache();
        Response::view('homepage', 200, true);
    }, true);


    /**
     * If there is no response for any controller
     * Just send a 404 response
    **/
    Controller::route(true, function() {
        Response::view('404', 404, true);
    }, true);


    /**
     * Invoke the controller queue
    **/
    Controller::invoke();

?>
