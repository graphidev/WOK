<?php 
    
    /**
     *    Welcome in WOK initialize file
     *
     * You don't have to edit any line of this.
     * To define some settings, please have a look to settings.php
     * To add / call some stuffs, please do it in options.php
     *
    **/

	define('WOK_MAJOR_VERSION', 0); // Major version
	define('WOK_MINOR_VERSION', 1); // Minor version
	define('WOK_RELEASE_VERSION', 0); // Release version
	define('WOK_EXTRA_VERSION', 'building'); // Extra version
	define('WOK_VERSION', WOK_MAJOR_VERSION.'.'.WOK_MINOR_VERSION.':'.WOK_RELEASE_VERSION); // Full version (without extra)
	

    /*
     * The following lines will define default path of essential tools.
     * We suggest you to let them as they are for a better compatibility.
    **/
	define('SERVER_ROOT', dirname(dirname(__FILE__))); // Define absolute root path
    define('PATH_CORE', '/core'); // Core path
	define('PATH_LIBS', '/libs'); // Libraries path
	define('PATH_TEMPLATE', '/template'); // Template path
    define('PATH_STATISTICS', '/statistics'); // Statistics data path
    

    /*
     * Once we have pathes, we can call essential libraries.
     * But you first need to have a settings.php file. 
     * Thereof contains all required constants to have functionnal libraries. 
     * Without it, some troubles may appear.
    **/
    if(file_exists(SERVER_ROOT.PATH_CORE.'/settings.php')):
        
        /**
         * Quick function to call default librairies
        **/
        function load_core_library($name) {
            if(file_exists(SERVER_ROOT.PATH_CORE."/$name.php")):
                require_once SERVER_ROOT.PATH_CORE."/$name.php";
                return true;
            else:
                return false;
            endif;
        }
        

        /**
         * All right ! Settings file exists.
         * We can load settings and required libraries
        **/
        load_core_library('settings'); // Framework settings
        load_core_library('utilities'); // Framework fonctions
        load_core_library('compatibility'); // PHP compatibility functions
        load_core_library('timezones'); // Timezones code / name


        /**
         * Particular cases which require some adjustements or conditions
        **/
        if(!function_exists('json_decode') && !function_exists('json_encode')):
			load_core_library('json');
		endif;
      

        /**
         * Send default headers as charset
         * Define a date timezone
         * Start sessions
        **/
        if(!headers_sent()):
            header("Content-Type: text/html; charset=utf-8"); // Send charset
            date_default_timezone_set(SYSTEM_TIMEZONE); // Define date timezone
            session_start(); // Start sessions
        endif;
        

        /**
         * Once everything is fine loaded, we call the options file.
         * This one will be used to add your own stuffs.
        **/
        if(file_exists(SERVER_ROOT.'/core/options.php')):
			require_once(SERVER_ROOT.'/core/options.php');
		endif;
        
    endif;

    /**
     * That's it !
    **/

?>