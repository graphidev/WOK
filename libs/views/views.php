<?php
    
    /**
     * 
     * This function allow you to specify your own MVC template files according to HTTP request
     *
     * You can access to different parameters and functions such as :
     *
     * $GLOBALS['_GET']['REQUEST']
     * $GLOBALS['_GET']['PARAMETERS']
     * $GLOBALS['_POST']
     * get_page()
     * get_parameter($name)
     * tpl_static_page()
     * ...
     *
     * The only obligation is that the function must return 
     * true if it call a page
     * false if it don't
     *
    **/

    function views() {
        
        /**
         * Example : redefine the default home page
        **/        
        if(get_request() == '' && get_parameter('no_homescreen') != true):
            
            tpl_static_page('homescreen');
            
            return true;

        else:
            return false;
        endif;
        
    }

?>