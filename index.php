<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <licence.txt>
    **/

    if(! require_once 'framework/init.php' )
        trigger_error('Framework settings not available', E_USER_ERROR);

    /**
     * Instanciate entry point
     * and load application services
    **/
    $request    = new Framework\Runtime\Request;
    $router     = require_once 'var/routes.php';

    $services   = require_once 'var/services.php';
    $services->register('request', $request);
    $services->register('router',  $router);
    $services->register('session',  new Framework\Utils\Session);
    $services->register('cookies',  '\Framework\Utils\Cookies');

    if(!$services->has('events')) {
        $events = new \Framework\Services\Events;
        $services->register('events', $events);
    }

    /**
     * Instanciate application
    **/
    $app = new Framework\Core\Application( $services );



    /**
     * Execute the application
    **/
    $app->run(function() use($app, $router, $request) {

        /**
         * Look for a defined route
         * Execute the matched route or return 404
        **/
        $route = $router->find(array(
            'path'      => $request->path,
            'method'    => $request->method,
            'domain'    => $request->domain,
        ));

        if(!$route)
            trigger_error('This request ('.$request.') has not any associated route', E_USER_ERROR);

        // Execute controller's action
        $response = $app->exec($route->controller, $route->action, $route->parameters);

        // No response sent
        if(is_null($response))
            trigger_error(
                'Controller '.$route->controller.'->'.$route->action
                .' didn\'t returned any response', E_USER_ERROR);

        return $response;

    });
