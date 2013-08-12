<?php
    
    /**
     * This is the preload file. 
     * It allow you to do anything you want before calling templates
    **/
    
    function controller() {
        if(get_request() == '' || get_request() == '/'):
            tpl_static_page('discover');
            return true;
        else:
            return false;
        endif;
    }
    
?>