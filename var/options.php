<?php

    /**
     * This is the option file. 
     * It allow you to use some configuration functions
     * You also can define some constants here
    **/
    

    /**
     * Manage error reports
     * This can be extended with custom callback :
     * use Console::handler($level, $callback)
    **/
    Console::init();

    /**
     * End callback
     * this function will be called at the end of PHP execution
    **/
    register_shutdown_function(function() {
        Console::register(); // Register errors logs
    });
    
    /**
     * Composer libraries autoloader
    **/
    if(file_exists($autoloader = SYSTEM_ROOT.'/vendor/autoload.php'))
        $loader = require_once $autoloader;


    /**
      * Set default locale and time zone
      * Theses informations may be updated by using Locales
    **/
     if(!Session::exists('language') && Cookie::exists('language', true)):
        Session::set('language', Cookie::get('language'));

    else:
        
        $languages = get_accepted_languages(explode(' ', SYSTEM_LANGUAGES));
        
        if(!empty($languages))
            $language = array_shift($languages);
            
        else
            $language = SYSTEM_DEFAULT_LANGUAGE;
        
        Session::set('language', $language);

        if(!Cookie::exists('language'))
            Cookie::set('language', $language, 15811200);        

    endif;

    setLocale(LC_ALL, Session::get('language').'.UTF-8');
    @date_default_timezone_set(SYSTEM_TIMEZONE);
    

    /**
     * Allow zlib compression
     * This should be made in php.ini !
    **/
    ini_set("zlib.output_compression", "On");


?>