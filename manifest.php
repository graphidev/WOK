<?php
    
    /**
     * This is the preload file. 
     * It allow you to do anything you want before calling templates
    **/

    /**
     * Set language controller
    **/
    Controller::assign(function($request) {
        return Request::param('_lang');
    }, function() {
        Session::language(Request::param('_lang'));
    }, false);

    
    /**
     * Sample of resource response usage
    **/
    Controller::assign(function($request) {
        return (Request::$domain == 'static.'.SERVER_DOMAIN ? true : false);
    }, function($request) {
        Response::resource($request);
    },true);
    
    
    /**
     * Redefine homepage controller (otherwise: homepage template)
    **/
    Controller::assign(function($request) {
        return ($request == '' || $request == '/' ? true : false);
    }, function() {
        //Response::view('discover');
        Request::$URI = 'discover';
    }, false);

    
    /**
     * Get page content controller
    **/
    Controller::assign(true, function($request) {
        
        Controller::inc('facebook/facebook');
        
        if(substr($request, -1) == '/')
            $request = substr($request, 0, -1);
                    
        $file = root(PATH_RESOURCES.'/data/'.Session::$language."/$request.json");
        if(file_exists($file)):
            $data = json_decode(file_get_contents($file), true);
            $json = array(
                'title' => 'Discover WOK',
                'content' => $data
            );
            //exit(json_encode($json));
            Response::assign(array(
                'title' => (!empty($data['title']) ? $data['title'] : null),
                'content' => (!empty($data['content']) ? PSDF::parse($data['content']) : null)
            ), true);
        endif;
            
    }, false);
    
?>