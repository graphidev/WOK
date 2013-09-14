<?php 
    /**
     *    Welcome in WOK initialize file
     *
     * You don't have to edit any line of this file.
     * To define some settings, please have a look to settings.php
     * To add / call some options, please use options.php
     *
    **/
    
	const WOK_MAJOR_VERSION        = 0; // Major version
	const WOK_MINOR_VERSION        = 4; // Minor version
	const WOK_RELEASE_VERSION      = 1; // Release version
	const WOK_EXTRA_RELEASE        = 'building'; // Extra version
    
    // Define full WOK version (without extra release)
	define('WOK_VERSION', WOK_MAJOR_VERSION.'.'.WOK_MINOR_VERSION.':'.WOK_RELEASE_VERSION);
	
    
    /*
     * The following lines will define default path of essential tools.
     * We suggest you to let them as they are for a better compatibility.
    **/

    // Define absolute root path
	define('SYSTEM_ROOT', dirname(__DIR__)); 

    const PATH_CORE             = '/core'; // Core path
	const PATH_LIBRARIES        = '/libraries'; // Libraries path
    const PATH_DATA             = '/data'; // Data's directory path
    const PATH_LOCALES          = '/languages'; // Languages' files directory
	const PATH_TEMPLATES        = '/templates'; // Template's directory path
    const PATH_FILES            = '/files'; // Files' directory
    const PATH_RESOURCES        = '/resources'; // Resources' directory
    const PATH_TMP_FILES        = '/files/tmp'; // Temporary files' directory
    const PATH_APIS             = '/api'; // API's path
    const PATH_LOGS             = '/logs'; // PHP logs directory

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
        require_once SYSTEM_ROOT.PATH_CORE . '/settings.php'; // Framework settings
        
        require_once SYSTEM_ROOT.PATH_CORE . '/compatibility.php'; // PHP compatibility functions
        require_once SYSTEM_ROOT.PATH_CORE . '/utilities.php'; // Framework fonctions
        require_once SYSTEM_ROOT.PATH_CORE . '/treatments.php'; // Treatments functions
        
        spl_autoload_register(function($name) {
            $name = strtolower($name);
            if(file_exists(SYSTEM_ROOT.PATH_CORE . "/$name.php"))
                require_once SYSTEM_ROOT.PATH_CORE . "/$name.php";
        });
        /*
        require_once SYSTEM_ROOT.PATH_CORE . '/request.php'; // Framework request class
        require_once SYSTEM_ROOT.PATH_CORE . '/controller.php'; // Framework controller class
        require_once SYSTEM_ROOT.PATH_CORE . '/response.php'; // Response class
        require_once SYSTEM_ROOT.PATH_CORE . '/session.php'; // Session class
        require_once SYSTEM_ROOT.PATH_CORE . '/locales.php'; // Locales class
        
        require_once SYSTEM_ROOT.PATH_CORE . '/psdf.php'; // PSDF Treatment
        require_once SYSTEM_ROOT.PATH_CORE . '/file.php'; // File manager class
        require_once SYSTEM_ROOT.PATH_CORE . '/mail.php'; // Mail class
        */

        require_once SYSTEM_ROOT.PATH_CORE . '/console.php'; // Framework logger
        require_once SYSTEM_ROOT.PATH_CORE . '/timezones.php'; // Timezones code / name
        

        /**
         * Particular cases which require some adjustements or conditions
        **/
        if(!function_exists('json_decode') && !function_exists('json_encode'))
			require_once SYSTEM_ROOT.PATH_CORE . "/json.php"; // JSON functions

        /**
         * Start and send every required headers.
        **/
        if(!headers_sent()):
            @date_default_timezone_set(SYSTEM_TIMEZONE); // Define date timezone
        endif;
    
        /**
         * Once everything is fine loaded, we call the options file.
         * This one will be used to add your own stuffs.
        **/
        if(file_exists(SYSTEM_ROOT.PATH_CORE.'/options.php'))
			require_once(SYSTEM_ROOT.PATH_CORE.'/options.php');
        
    endif;

    /**
     * That's it !
    **/

?>