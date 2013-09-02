<?php
    
    /**
     * This is the preload file. 
     * It allow you to do anything you want before calling templates
    **/
    
    /**
     * Set API's controller
    **/
/*
    Controller::assign(function($query) {
        $host =     (Access::$domain == 'api.'.SERVER_DOMAIN ? true : false);
        $path =     preg_match('#^api/(.+)?$#', $query);
        
        return $host or $path;
        
    }, function($query) {
        Route::api($query);
    }, true);
    */
    /*
    Controller::assign(function() {
        return (preg_match('#^api/locales(/(.+))?$#', Access::$request) ? true : false);
    }, function($query) {
        header('Content-type: application/json');
        Route::api('locales');
    }, true);
    */

    Controller::assign(function($request) {
        return Request::param('_lang');
    }, function() {
        Session::language(Request::param('_lang'));
    }, false);
    
    Controller::assign(function($request) {
        return (Request::$domain == 'static.'.SERVER_DOMAIN ? true : false);
    }, function($request) {
        Response::resource($request);
    },true);
    

    Controller::assign(function($request) {
        return ($request == '' || $request == '/' ? true : false);
    }, function() {
        Response::view('discover');
    }, true);
    
?>