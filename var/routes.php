<?php

    use \Router\Router;
    use \Router\Route;

    /**
     * Generate routes from settings;
    **/
    return function($settings) {

        /**
         * Initialize router engine
        **/
        $routes = new Router();


        /**
         * Homepage route
        **/
        $routes->addRoute(
            'Application\Nodes->home',
            new Route(
                '/', array(), ['GET', 'POST']
                //$settings->application->domains['default']
            )
        );


        /**
         * Default page route
         * HTTP GET|POST domain.tld/{page}
        **/
        $routes->addRoute(
            'Application\Application->page',
            new Route(
                '/{page}', array(
                    'user'  => '[a-z0-9]+',
                    'page' => '[a-z0-9_\/-]+'
                ),
                ['GET']
                //$settings->application->domains['default']
            )
        );


        /**
         * Default not found route
        **/
        $routes->addRoute(
            'Application\Application->pageNotFound',
            new Route(
                '/{path}', [
                    'path'      =>  '.+'
                ],
                ['GET', 'POST', 'HEAD']
                //$settings->application->domains['default']
            )
        );

        return $routes;

    };
