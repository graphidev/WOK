<?php

	/**
     * Initialize WOK
    **/
	require_once 'core/init.php';


    /**
     * Foremost, we'll check if all the folders which must be writable are writable
    **/
    if(!@is_writable(PATH_TMP) || !@is_writable(root(PATH_LOGS))):
        new Response;
        Response::view('503', 503);
        trigger_error('not writable system folders', E_USER_ERROR);
    endif;

    /**
     * Inititialize Required classes
    **/
    new Console; // Start handling errors
    Session::start(); // Initialiez session
    Manifest::init(); // Initialize manifest data
    Request::init(); // Initialize request informations
     


    /**
     * Set Custom things
    **/
    if(file_exists(root(PATH_VAR.'/manifest.php')))
        require_once(root(PATH_VAR.'/manifest.php'));
    

    /**
     * Set static pages controller
    **/
    Controller::assign(Request::get('action') == 'static', function() {
        Response::view(Request::get('URI'), 200, true);
    }, true);

    
    /**
     * Set predefined controllers
    **/
    Controller::assign(Request::get('action') && Request::get('action') != 'static' ? true : false, function() {
        list($name, $action) = explode(':', Request::get('action'));
        if(file_exists(root(PATH_CONTROLLERS."/$name.ctrl.php"))):
            return Controller::call($name, $action);
        else:
            trigger_error("Controller '$name' not found", E_USER_ERROR);
        endif;
    }, false);
    

    /**
     * Set default homepage controller
    **/
    Controller::assign(Request::get('URI') == '' ? true : false, function() {
        new Response;
        Response::view('homepage', 200);
    }, true);


    /**
     * If there is no response for any controller
     * Just send a 404 response
    **/
    Controller::assign(true, function() {
        new Response;
        Response::view('404', 404);
    }, true);


    /**
     * Invoke the controller queue
    **/
    Controller::invoke();


    /**
     * Register console logs
    **/
    Console::register();

?>
