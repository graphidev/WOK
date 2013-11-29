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
        trigger_error('Bootstrap : not writable system folders', E_USER_ERROR);
    endif;

    /**
     * Inititialize Required classes
    **/
    new Console; // Start handling errors
    Session::start(); // Initialiez session
    Route::init(); // Initialize manifest data
    Request::init(); // Initialize request informations
     


    /**
     * Set Custom things
    **/
    if(file_exists(root(PATH_VAR.'/manifest.php')))
        require_once(root(PATH_VAR.'/manifest.php'));
    

    /**
     * Set predefined controllers
    **/
    Controller::assign(Request::get('action') ? true : false, function() {
        list($name, $action) = explode(':', Request::get('action'));
        if(file_exists(root(PATH_CONTROLLERS."/$name.ctrl.php"))):
            return Controller::call($name, $action);
        else:
            trigger_error("Controller '$name' not found", E_USER_ERROR);
        endif;
    }, false);
    

    /**
     * Set static pages controller
    **/
    Controller::assign(Request::get('URI') && Request::get('domain') == SYSTEM_DOMAIN ? true : false, function() {
        new Response;
        
        if(is_dir(root(PATH_TEMPLATES.'/'.substr(Request::get('URI'), 0, -1)))):         
            $dirname = dirname(Request::get('URI'));
            
            if(substr(Request::get('URI'), -1) == '/'):
                $filename = str_replace($dirname, '', substr(Request::get('URI'), 0, -1)); 
            else:
                $filename = str_replace($dirname, '', Request::get('URI'));
            endif;
            
            Response::view(str_replace('//', '/', Request::get('URI').$filename));

        else:   
           Response::view(Request::get('URI'));
    
        endif;
    }, true);
    

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
