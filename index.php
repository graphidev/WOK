<?php
    
	/**
     * Initialize WOK
    **/
	require_once 'core/init.php';
    
    /**
     * Initialise the framework environment
     * This is required in order to process
     * routing and dispatching request
    **/
    Request::parse();
    Router::instantiate();
    Console::init();

    /**
     * Start buffering
     * Response will be generated before they will
     * be send. If an error occured, the response 
     * will be erased and replaced by an error response.
    **/
    ob_start();

    /**
     * End callback
     * this function will be called at the end of PHP execution
    **/
    register_shutdown_function(function() { 
                
        /**
         * Register errors logs
         * This will change response if an
         * error occured from the beginning
        **/
        if(($error = Console::register()) && !SYSTEM_DEBUG) {
            
            ob_clean(); // Clean buffer
                                                
            Response::view('error', 500)->assign(array(
                //'code' => $e->getCode(),
                'message' => $error['message'],
            ))->render();
        }

    });
    

    /**
     * Ongoing maintenance 
     * Output a 503 response
    **/
    if(SYSTEM_MAINTENANCE) {
        Response::view('maintenance', 503)
            ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, 'maintenance')
            ->render();
        exit;
    }

    /**
     * Set Custom routes and filters
     * Note that XML manifest and in-app PHP manifest
     * can both be used on the same instance.
     * However the XML one will be parsed first
    **/
    if(file_exists(root(PATH_VAR.'/manifest.php')))
        require_once(root(PATH_VAR.'/manifest.php'));

    if(file_exists(root(PATH_VAR.'/filters.php')))
        require_once(root(PATH_VAR.'/filters.php'));

    /**
     * Output response according to controller's return
     * Return false if no route have been find
    **/
   // try {
        if(!Router::dispatch()) {
            Response::view('404', 404)
                ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, '404')
                ->render();   
        }
    /*}
    catch(Exception $e) {
        Response::view('404', 404)->assign(array(
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ))->render();   
    }
    */

    /**
     * Shutdown if there was at least one error
     * during the response generation
     * This will call the shutdown callback
     * and change the response content
    **/
    if(Console::getLastError())
        exit;


    /**
     * Output response
     * The response may have been replaced 
     * by the error manager (Console)
    **/
    ob_end_flush();
    
?>
