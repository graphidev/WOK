<?php
    
    /**
     * This is the preload file. 
     * It allow you to do anything you want before calling templates
    **/

    Controller::add(function() {
        return ($_SERVER['HTTP_HOST'] == 'api.'.SERVER_DOMAIN 
                && preg_match('#^locales/(.+)$#', get_request())
                && get_request() != 'locales/all' ? true : false);
    }, function($query) {
            $locale = preg_replace('#^locales/(.+)$#', '$1', get_request());
            if($locale != 'all'):
                $translation = root(PATH_LOCALES."/$locale.json");
                if(file_exists($translation)):
                    header('Content-type: application/json');
                    readfile($translation);
                else:
                    header('Content-type: application/json');
                    echo json_encode(array('error'=>true, 'code'=>404, 'info'=>'API not found'));
                endif;
            endif;
        }, true);
    
    Controller::add(($_SERVER['HTTP_HOST'] == 'api.'.SERVER_DOMAIN), function($query) {
            $api = root(PATH_APIS.'/'.get_request().'.php');
            if(file_exists($api)):
                include_once($api);
            else:
                header('Content-type: application/json');
                echo json_encode(array('error'=>true, 'code'=>404, 'info'=>'API not found'));
            endif;
        }, true);

    Controller::add(get_request() == '' || get_request() == '/', function() {
        tpl_static_page('discover');
    }, true);
    
?>