<?php

    /**
     * The routes file must contains and return
     * your app routes collection.
    **/


    /**
     * Load router engine
    **/
    $routes = new Framework\Core\Router();

    /**
     * Journal home
     * => domain.tld/
    **/
    $routes->register('Journal:home', array(
        'path'          => '/',
    ));

    /**
     * Session
     * => cms.domain.tld/account
    **/
    $routes->register('Session:login', array(
        'path'          => '/account/',
        'domain'        => 'cms.'.SYSTEM_DOMAIN
    ));

    /**
     * Backoffice : post
     * => cms.domain.tld/posts/:category/:id
    **/

    /**
     * Backoffice : categories
     * => editor.domain.tld/categories/:category/:id
    **/

    /**
     * Journal search
     * => domain.tld/recherche/
    **/
    $routes->register('Journal:search', array(
        'path'          => '/recherche/:query',
        'parameters'    => array(
            'query'  => '.+'
        )
    ));

    /**
     * Journal indexes
     * => domain.tld/nodename/
    **/
    $routes->register('Journal:category', array(
        'path'          => '/:name/',
        'parameters'    => array(
            'name'  => '[a-z0-9_\-]+'
        )
    ));


    /**
     * Journal article
     * => domain.tld/nodename/post-title
    **/
    $routes->register('Journal:article', array(
        'path'          => '/:category/:title',
        'parameters'    => array(
            'category'  => '[a-z0-9_\-]+',
            'title' => '[a-z0-9\-]+',
        )
    ));

    /**
     * Static pages
     * => domain.tld/path/to/page
    **/
    $routes->register('Main:media', array(
        'path'          => '/media/:path',
        'parameters'    => array(
            'path'  => '.+' //'[a-z0-9_/\-]+'
        )
    ));

    /**
     * RSS flux
     * => api.domain.tld/rss/articles.xml(?type=:nodename)
     * => domain.tld/rss.xml
     * => domain.tld/nodename/rss.xml
    **/
    $routes->register('Journal:RSS', array(
        'path'          => '/rss.xml',
    ));


    /**
     * Journal node RSS flux
    **/
    $routes->register('Journal:RSSNode', array(
        'path'          => '/:type/rss.xml',
        'parameters'    => array(
            'type'  => '[a-z0-9\-]+',
        )
    ));


    /**
     * Static pages
     * => domain.tld/path/to/page
    **/
    $routes->register('Main:page', array(
        'path'          => '/:path',
        'parameters'    => array(
            'path'  => '[a-z0-9_/\-]+'
        )
    ));


    /**
     * Static pages
    **/
    /*
    $routes->register('Base:page', array(
        'path'          => '/:page',
        'parameters'    => array(
            'page'  => '[a-z0-9/\-]+'
        )
    ));
    */


    /**
     * Accounts routes
    **/
    /*
    $routes->register('Users:register', array(
        'path'          => '/subscribe',
        'method'        => 'post'
    ));
    */

    /**
     * Default controller/action route
    **/
    /*
    $routes->register('controller:action', array(
        'path'          => '/controller/:action/(:optional)?',
        'parameters'    => array(
            'controller'    => '[a-z0-9]+',
            'action'        => '[a-z0-9]+',
            'optional'      => '.+',
        ),
        'filters' => array(
            function($route, $services) {
                return $route->parameters->controller.':'.$route->parameters->action;
            }
        )
    ));
    */


    /**
     * Locale filter
     * This filter check the availability of a locale
     * It also redirects to the right URL from the user language
    **/
    $routes->filter('locales', function($route, $services) {

        if(!in_array($route->parameters->locale, explode(',', SYSTEM_LANGUAGES))
           || $route->parameters->locale != Session::get('language')) {

            $route->parameters->locale = Session::get('language');

            $url = $service->get('router')->url($route->action, $parameters);
            return Response::redirect($url);

        }

    });

    /*
    $router->filter('ajax', function($route, $services) {
        return $services->get('request')->ajax();
    });
    */


    /**
     * Return the routes collection
     * and their filters
    **/
    return $routes;
