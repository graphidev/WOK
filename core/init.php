<?php 
    /**
     *    Welcome in WOK initialize file
     *
     * You don't have to edit any line of this file.
     * To define some settings, please have a look to settings.php
     * To add / call some options, please use options.php
     *
    **/

	define('WOK_MAJOR_VERSION', 0); // Major version
	define('WOK_MINOR_VERSION', 3); // Minor version
	define('WOK_RELEASE_VERSION', 1); // Release version
	define('WOK_EXTRA_VERSION', 'building'); // Extra version
	define('WOK_VERSION', WOK_MAJOR_VERSION.'.'.WOK_MINOR_VERSION.':'.WOK_RELEASE_VERSION); // Full version (without extra)
	

    /*
     * The following lines will define default path of essential tools.
     * We suggest you to let them as they are for a better compatibility.
    **/
	define('SYSTEM_ROOT', dirname(dirname(__FILE__))); // Define absolute root path
    define('PATH_CORE', '/core'); // Core path
	define('PATH_LIBS', '/libraries'); // Libraries path
    define('PATH_DATA', '/data'); // Data's directory path
	define('PATH_TEMPLATE', '/template'); // Template's directory path
    define('PATH_FILES', '/files'); // Files' directory
    define('PATH_TMP_FILES', PATH_FILES.'/tmp'); // Temporary files' directory


    /*
     * Once we have pathes, we can call essential libraries.
     * But you first need to have a settings.php file. 
     * Thereof contains all required constants to have functionnal libraries. 
     * Without it, some troubles may appear.
    **/
    if(file_exists(SYSTEM_ROOT.PATH_CORE.'/settings.php')):
        
        /**
         * All right ! Settings file exists.
         * We can load settings and required libraries
        **/
        require_once SYSTEM_ROOT.PATH_CORE."/settings.php"; // Framework settings
        require_once SYSTEM_ROOT.PATH_CORE."/utilities.php"; // Framework fonctions
        require_once SYSTEM_ROOT.PATH_CORE."/compatibility.php"; // PHP compatibility functions
        require_once SYSTEM_ROOT.PATH_CORE."/treatments.php"; // Treatments functions
        require_once SYSTEM_ROOT.PATH_CORE."/timezones.php"; // Timezones code / name
        require_once SYSTEM_ROOT.PATH_CORE."/file.php"; // File class
        require_once SYSTEM_ROOT.PATH_CORE."/mail.php"; // Mail class


        /**
         * Particular cases which require some adjustements or conditions
        **/
        if(!function_exists('json_decode') && !function_exists('json_encode')):
			require_once SYSTEM_ROOT.PATH_CORE."/json.php"; // JSON functions
		endif;
      

        /**
         * Start and send every required headers.
        **/
        if(!headers_sent()):
            session_start(); // Start sessions
            header("Content-Type: text/html; charset=utf-8"); // Send charset
            @date_default_timezone_set(SYSTEM_TIMEZONE); // Define date timezone
        endif;
        

        /**
         * Once everything is fine loaded, we call the options file.
         * This one will be used to add your own stuffs.
        **/
        if(file_exists(SYSTEM_ROOT.'/core/options.php')):
			require_once(SYSTEM_ROOT.'/core/options.php');
		endif;
        
    endif;

    /**
     * That's it !
    **/

?>