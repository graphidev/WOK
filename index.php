<?php

	/**
     * Initialize WOK
    **/
	require_once 'core/init.php';


    /**
     * Foremost, we'll check if all the folders which must be writable are writable
    **/

    if(!@is_writable(PATH_TMP) || !@is_writable(root(PATH_LOGS)) || !@is_writable(root(PATH_FILES)) || !@is_writable(root(PATH_TMP_FILES))):
        Response::view('503', 503);
        Console::fatal('Bootstrap : not writable system folders');
    endif;
    
    /**
     * Init the Request
     * Init session
    **/
    new Request;
    Session::start();


    /**
     * Set Custom controllers
    **/
    if(file_exists(root('/manifest.php')))
        require_once(root('/manifest.php'));    

    /**
     * Set default homepage controller
    **/
    Controller::assign(function($query) {
        return empty($query);
    }, 
    function() {
        Response::view('homepage');
    }, true);
    

    /**
     * Set static pages controller
    **/
    Controller::assign(true, function($query) {
        /**
         * Directory, check the file with the same name in this directory
        **/
        if(is_dir(root(PATH_TEMPLATES.'/'.substr($query, 0, -1)))):         
            $dirname = dirname($query);
    
            if(substr(Request::$URI, -1) == '/'):
                 $filename = str_replace($dirname, '', substr($query, 0, -1));
            else:
                $filename = str_replace($dirname, '', $query);
            endif;
            
            Response::view(str_replace('//', '/', $query.$filename));
            
        /**
         * File, check the filename
        **/
        else:   
            Response::view($query);
    
        endif;
    }, true);
    

    /**
     * Invoke the controller queue
    **/
    Controller::invoke(Request::$URI);


    /**
     * Register console
    **/
    Console::register();

?>
