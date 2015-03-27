#!/usr/bin/env php
<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <licence.txt>
    **/

    /**
     * Prevent non CLI usage
    **/
    if(! (!getenv('SERVER_SOFTWARE') && (PHP_SAPI == 'cli' || (is_numeric(getenv('argc')) && getenv('argc') > 0))) )
       trigger_error('Non CLI usage prohibited', E_USER_ERROR);

    /**
     * Boot framework
    **/
    if(! require_once 'framework/init.php')
        trigger_error('Framework settings not available', E_USER_ERROR);


    /**
     * Output framework version
     * Use `-v` or `--version` syntax
    **/
    $cmd = getopt('v', array('version'));
    if(isset($cmd['v']) || isset($cmd['version'])) {
        echo WOK_VERSION.PHP_EOL;
        exit;
    }


    /**
     * Instanciate entry point
     * and load applications services
    **/
    $command    = new Framework\Core\Command($argv);
    $router     = require_once 'var/routes.php';

    $services   = require_once 'var/services.php';
    $services->register( 'command', $command );
    $services->register( 'router',  $router );


    /**
     * Instanciate application
    **/
    $app = new Framework\Core\Application( $services );
    use Framework\Core\Response;


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
                ->render(true);

        }


        /**
         * Look for a defined route
         * Execute the matched route or return 404
        **/
        $route = $router->match($command->module, $command->action, $command->parameters);

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

        return $response->render(true);
    });
