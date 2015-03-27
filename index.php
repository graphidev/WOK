<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <licence.txt>
    **/

    if(! require_once 'framework/init.php')
        trigger_error('Framework settings not available', E_USER_ERROR);


    /**
     * Instanciate entry point
     * and load applications services
    **/
    $request    = new Framework\Core\Request;
    $router     = require_once 'var/routes.php';

    $services   = require_once 'var/services.php';
    $services->register( 'framework:request', $request );
    $services->register( 'framework:router',  $router );

    if(!$services->has('events')) {
        $events = new \Framework\Services\Events;
        $services->register('framework:events', $events);
    }

    /**
     * Instanciate application
    **/
    $app = new Framework\Core\Application( $services );
    use Framework\Core\Response;


    // Call events
    $events->trigger('application->run:before', array($services));


    /**
     * Execute the application
    **/
    $app->run(function() use($app, $router, $request) {

        /**
         * Ongoing maintenance
         * Returns a default maintenance response
        **/
        if(SYSTEM_MAINTENANCE) {

            return Response::view('maintenance', 503)
                ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, 'maintenance')
                ->render();

        }


        /**
         * Look for a defined route
         * Execute the matched route or return 404
        **/
        $route = $router->find(array(
            'path'      => $request->path,
            'method'    => $request->method,
            'domain'    => $request->domain,
        ));

        // Execute controller's action
        if($route) {

            $response = $app->exec($route->controller, $route->action, $route->parameters);

            // No response sent
            if(is_null($response))
                trigger_error(
                    'Controller '.$route->controller.':'.$route->action
                    .' didn\'t returned any response', E_USER_ERROR);

        }

        // Route not found
        else {
            $response = Response::view('404', 404)
                ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC, '404');
        }

        return $response->render();

    });
