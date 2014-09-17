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
        $response = Response::view('maintenance', 503)
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
             * This may be a null value (not recommended), 
             * or a custom response (HTML, JSON, XML, ...).
             * Please check Response class documentation.
            **/
            Router::match(function($controller, $parameters) {
                
                if($controller instanceof Response)
                    $controller->render();
                
                elseif(is_null($response = call_user_func_array($controller, $parameters)))
                    Response::null(200)->render();
                
                elseif($response instanceof Response)
                    $response->render();   

                else trigger_error('Controller returned value must be a Response object', E_USER_ERROR);
                                
            });    

            
        } catch(Exception $e) {
            
            Response::view($e->getCode(), $e->getCode())->assign(array(
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ))->render();
            
        }
        
    }
    
?>
