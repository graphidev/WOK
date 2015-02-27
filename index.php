<?php

	/**
	 * Load settings and define autoloader
	**/
	if(! require_once 'core/init.php' )
		exit('System settings not available');

	/**
	 * Load HTTP requierements
	**/
	require_once 'core/http.php';

	App::init(new Request);

	App::router()->register()

	App::request()

	Request::set($entrypoint);

	/**
	 * Initialize request form the entry point
	 * As of HTTP request, every parameter will be calculated
	**/
	Console::init(); // Forced initialization
	Request::parse();

	$stdin = fopen('php://stdin', 'r');
	$d = stream_get_contents($stdin);
	var_dump($stdin,$d);
	exit;

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
            ))->cachecache(Response::DISABLE_CACHE, Response::CACHE_PRIVATE, false)
			->render(true);
        }

    });


    /**
     * Ongoing maintenance
     * Output a 503 response
    **/
    if(SYSTEM_MAINTENANCE) {
        Response::view('maintenance', 503)
            ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, 'maintenance')
            ->render(true);
        exit;
    }

    /**
     * Set Custom routes and filters
     * Note that XML manifest and in-app PHP manifest
     * can both be used on the same instance.
     * However the XML one will be parsed first
    **/
    if(file_exists(root(PATH_VAR.'/manifest.php')))
    	require_once root(PATH_VAR.'/manifest.php');

    if(file_exists(root(PATH_VAR.'/filters.php')))
        require_once(root(PATH_VAR.'/filters.php'));

    /**
     * Output response according to controller's return
     * Return false if no route have been find
    **/
	$route = Router::find(Request::uri(), Request::method(), Request::domain());

	if(!$route) // Route not found
		$response = Response::view('404', 404)->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, '404');

	elseif($route instanceof Response) // Rerouted
		$response = $route;

	elseif($route instanceof StdClass) // Route found
		$response = Dispatcher::run($action->controller, $action->parameters);
	
	// Generate response
	$response->render(true);

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

	exit;
