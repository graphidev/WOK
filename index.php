<?php

	/**
     * Initialize WOK
    **/
	require_once 'core/init.php';


    /**
     * Inititialize Required classes
    **/
    new App; // Initialize the app
    new Session; // Initialize session
    new Request; // Initialize request informations


    /**
     * Set Custom things
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
        Response::view(Request::get('URI'), 200, true);
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
    Controller::route(Request::get('URI') == '' ? true : false, function() {
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


    /**
     * Register console logs
    **/
    Console::register();

?>
