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

    /*
    $app->before(function($services) use($settings) {


        if($settings->app->basedir) {

            $request    = $services->get('request');
            $uri        = $request->getUri();
            $path       = mb_substr($uri->getPath(), mb_strlen($settings->app->basedir));

            $uri->withPath($path);
            $request->withUri($uri);
            $services->register('request', $this->request);

        }

    });
    */


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

    /*
     $app->after(function($services) {});
    */
