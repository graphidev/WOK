<?php

	/**
     * Initialize WOK
    **/
	require_once 'core/init.php';
    
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

?>
