<?php


    /**
     * Load settings and define autoloader
    **/
    if(! require_once 'core/init.php' )
        exit('System settings not available');


    /**
     * Initialize application
     * Set an entry point method
    **/
    new Request;
    $app = App::init( new Request );


    $app->register('request', new Request);

    App::get('request')->method();
    App::request()->getParameter('page');
    App::request()->parameters;
    App::router()->register('controller:action', function());

    /**
     * Set Custom routes and filters
     * Note that XML manifest and in-app PHP manifest
     * can both be used on the same instance.
     * However the XML one will be parsed first
    **/
    if(file_exists(root(PATH_VAR.'/routes.php')))
        require_once root(PATH_VAR.'/routes.php');

    if(file_exists(root(PATH_VAR.'/filters.php')))
        require_once root(PATH_VAR.'/filters.php');


    /**
     * Register errors shutdown callback
    **/
    $app->shutdown(function($errors) {

        /* @TODO send mail */

        Response::view('error', 500)->assign(array(
            'errors' => $errors
        ))->cachecache(Response::DISABLE_CACHE, Response::CACHE_PRIVATE, false)
        ->render(true);

    });


    /**
     * Output response
    **/
    $app->run(function($request) {

        // Ongoing maintenance
        if(SYSTEM_MAINTENANCE) {

            Response::view('maintenance', 503)
                ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, 'maintenance')
                ->render(true);

        }

        // Controllers access available
        else {

            $response = Router::find($request->path(), $request->method(), $request->path());

            if( !$response ) // No route available
                $response = Response::view('404', 404)
                    ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, '404');

            return $response->render(true);
            
        }

    });
