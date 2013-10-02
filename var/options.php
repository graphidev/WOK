<?php

    /**
     * This is the option file. 
     * It allow you to add some configuration constants and functions
    **/


    /**
     * Locale shortcut
    **/
    function _e($path, $data = array()) {
        return Locales::_e($path, $data);
    }
    
?>