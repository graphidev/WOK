<?php

	/**
     * Initialize WOK
    **/
	require_once 'core/init.php';


    /**
     * Foremost, we'll check if all the folders which must be writable are writable
    **/
    if(!@is_writable(PATH_TMP) || !@is_writable(root(PATH_LOGS)) || !@is_writable(root(PATH_FILES)) || !@is_writable(root(PATH_TMP_FILES))):
        $response = new Response;
        $response->view('503', 503);
        trigger_error('Bootstrap : not writable system folders', E_USER_ERROR);
    endif;
    

    /**
     * Init Required classes
    **/
    new Console; // Start handling errors
    Session::start(); // Initialiez session
    Route::init(); // Initialize manifest data
    Request::init(); // Initialize request informations
    $controller = new Controller; // Initialize controller
     


    /**
     * Set Custom things
    **/
    if(file_exists(root(PATH_VAR.'/manifest.php')))
        require_once(root(PATH_VAR.'/manifest.php'));
    

    /**
     * Set predefined controllers
    **/
    $controller->assign(Request::get('action') ? true : false, function() use($controller) {
        list($name, $action) = explode(':', Request::get('action'));
        if(file_exists(root(PATH_CONTROLLERS."/$name.ctrl.php"))):
            return $controller->call($name, $action);
        else:
            Console::error("Controller '$name' not found");
        endif;
    }, false);
    

    /**
     * Set default static pages controller
    **/
    $controller->assign(Request::get('URI') ? true : false, function() {
        $response = new Response;
        
        if(is_dir(root(PATH_TEMPLATES.'/'.substr(Request::get('URI'), 0, -1)))):         
            $dirname = dirname(Request::get('URI'));
            
            if(substr(Request::get('URI'), -1) == '/'):
                $filename = str_replace($dirname, '', substr(Request::get('URI'), 0, -1)); 
            else:
                $filename = str_replace($dirname, '', Request::get('URI'));
            endif;
            
            $response->view(str_replace('//', '/', Request::get('URI').$filename));

        else:   
           $response->view(Request::get('URI'));
    
        endif;
    }, true);
    

    /**
     * Set default homepage controller
    **/
    $controller->assign(Request::get('URI') == '' ? true : false, function() {
        $response = new Response;
        $response->view('homepage', 200);
    }, true);


    /**
     * Invoke the controller queue
    **/
    $controller->invoke();


    /**
     * Register console logs
    **/
    Console::register();

?>
