<?php
    
    /**
     * This is the file to define routes, filters an patterns.
     * You can choose one of the two methods for routes définition :
     * with the XML manifest file thanks to Manifest::load() method
     * or with custom routes définition (Manifest::register(...)).
     *
     * Note : 
     * You can also use these both methods but this is not adviced
     * in order to keep a one place routes définition.
    **/ 
    
    // Load the XML manifest file
    Manifest::load();

    /**
     * Definition of the locale filter
     * This filter check the availability of a locale
     * It also redirect to the write URL from the user language
    **/
    Manifest::filter('locale', function($route, $parameters) {
        if(!in_array($parameters['locale'], explode(',', SYSTEM_LANGUAGES)) 
           || $parameters['locale'] != Session::get('language')) {
            
            $parameters['locale'] = Session::get('language');
            return Response::redirect(Manifest::url($route['action'], $parameters));
            
        }
    });

    /* Example of patterns
        Manifest::pattern('id', '[\d]+');
        Manifest::pattern('locale', '[a-z]{2,3}_[A-Z]{2,3}');
    */
