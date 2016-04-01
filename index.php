<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <licence.txt>
    **/

    if(! require_once 'framework/init.php' )
        trigger_error('Framework settings not available', E_USER_ERROR);

    /**
     * Instanciate entry point
     * and load application services
    **/
    $request    = new Message\Request();
    $settings   = new Application\Settings(require_once 'var/settings.php');
    $router     = call_user_func(require_once 'var/routes.php', $settings);
    $services   = call_user_func(require_once 'var/services.php', $settings);


    /**
     * Register framework services
    **/
    $services->register('request', $request);
    $services->register('router',  $router);


    /**
     * Instanciate application
    **/
    $app = new Application\Application( $services );


    /**
     * Implements application middlewares
    **/
    if(file_exists($before = root(PATH_VAR.'/before.php')))
        $app->before(require $before);

    if(file_exists($after = root(PATH_VAR.'/after.php')))
        $app->after(require $after);


    /**
     * Define the application script
    **/
    $app->action(function() use($request, $router) {

        $action = $router->fetch(
            $request->getMethod(),
            $request->getUri()->getHost(),
            $request->getUri()->getPath()
        );

        if(!$action)
            trigger_error('This request ('.$request->getUri().') has not any associated route', E_USER_ERROR);

        return $action;

    });


    /**
     * Run the application
    **/
    $app->run();
