<?php
    
    /**
     * This is the preload file. 
     * It allow you to do anything you want before calling templates
    **/ 

    /**
     * Define Locale::_e shortcut
    **/
    function _e($locale, $data = array()) {
        return Locales::_e($locale, $data);
    }


    
?>