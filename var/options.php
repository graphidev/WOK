<?php

    /**
     * This is the option file. 
     * It allow you to use some configuration functions
     * You also can define some constants here
    **/

    /**
      * Set default locale and time zone
      * This information may be updated by using Locales
    **/
    setLocale(LC_ALL, SYSTEM_DEFAULT_LANGUAGE.'.UTF8');
    @date_default_timezone_set(SYSTEM_TIMEZONE);


    /**
     * Allow zlib compression
     * This should be made in php.ini !
    **/
    ini_set("zlib.output_compression", "On");

    
    /**
     * End callback
     * this function will be called at the end of PHP execution
    **/
    register_shutdown_function(function() {
        Console::register(); // Register errors logs  
    });

?>