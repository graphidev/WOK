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
    
	ob_start(); // Start buffering response
	
 	try {
		 
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
            if(!Router::dispatch()) {
                Response::view('404', 404)
                    ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, '404')
                    ->render();   
            }
			
		}

            
	} catch(Exception $e) {

		ob_clean();

		if(get_class($e) == 'ConsoleException') {
			Response::view('503', 503)->render();
		}
		else {

			Response::view('error', $e->getCode())->assign(array(
				'message' => $e->getMessage(),
				'code' => $e->getCode()
			))->render();

		}

	}

	ob_end_flush(); // Stop buffering response and send it
    
?>
