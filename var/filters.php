<?php

    /**
     * This file should be the only one where
     * filters could be defined. However, the filters
     * can be defined in the manifest file too (not the XML one)
    **/

    /**
     * Definition of the locale filter
     * This filter check the availability of a locale
     * It also redirect to the write URL from the user language
    **/
    Router::filter('locale', function($action, $parameters) {
        if(!in_array($parameters['locale'], explode(',', SYSTEM_LANGUAGES))
           || $parameters['locale'] != Session::get('language')) {

            $parameters['locale'] = Session::get('language');
            return Response::redirect(Manifest::url($route['action'], $parameters));

        }
    });


?>
