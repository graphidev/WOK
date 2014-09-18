<?php

	/**
     * Initialize WOK
    **/
	require_once 'core/init.php';

    /**
     * Initialement request environment
    **/
    Request::init();

    
    /**
     * Ongoing maintenance 
     * Output a 503 response
    **/
    if(SYSTEM_MAINTENANCE) {
        Response::view('maintenance', 503)
            ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, 'maintenance')
            ->render();
    }
    
    /**
     * Route to a controller
    **/
    else {
        
        /**
         * Set Custom routes and filters
         * This should be use for development routes. Prefere using 
         * XML manifest in order to keep the framework structure
        **/
        if(file_exists(root(PATH_VAR.'/manifest.php')))
            require_once(root(PATH_VAR.'/manifest.php'));
        
        try {
            
            /**
             * Output response according to controller's return
             * Return false if no route have been find
            **/
            if(!Router::dispatch()) {
                Response::view('404', 404)
                    ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, '404')
                    ->render();   
            }

            
        } catch(Exception $e) {
            
            Response::view($e->getCode(), $e->getCode())->assign(array(
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ))->render();
            
        }
        
    }
    
?>
