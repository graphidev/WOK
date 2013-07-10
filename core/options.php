<?php

    /**
     * This is the option file. 
     * It allow you to add some configuration constants and functions
    **/
    
    function controller() {
        if(get_request() == ''):
            tpl_static_page('homescreen');
            return true;
        else:
            return false;
        endif;
    }
    
?>