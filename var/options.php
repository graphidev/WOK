<?php

    /**
     * This is the option file. 
     * It allow you to add some configuration constants and functions
    **/

    /**
      * Set default locale
      * This information may be updated by using Locales
    **/
    setLocale(LC_ALL, SYSTEM_DEFAULT_LANGUAGE.'.UTF8');

    /**
     * Start and send every required headers.
    **/
    if(!headers_sent())
        @date_default_timezone_set(SYSTEM_TIMEZONE); // Define date timezone   

    /**
     * Allow zlib compression
     * This should be made in php.ini !
    **/
    ini_set("zlib.output_compression", "On");

?>