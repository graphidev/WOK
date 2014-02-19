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
	const WOK_MINOR_VERSION        = 1; // Minor version
	const WOK_RELEASE_VERSION      = 1; // Release version
	const WOK_EXTRA_RELEASE        = 'RC'; // Extra version
    
    // Define full WOK version (without extra release)
	define('WOK_VERSION', WOK_MAJOR_VERSION.'.'.WOK_MINOR_VERSION.':'.WOK_RELEASE_VERSION);
	
    
    /*
     * The following lines will define default path of essential tools.
     * We suggest you to let them as they are for a better compatibility.
    **/

    // Define absolute root path
	define('SYSTEM_ROOT', dirname(__DIR__)); 

    const PATH_CORE             = '/core'; // Core path
    const PATH_VAR              = '/var'; // Config path
    const PATH_TMP              = '/var/tmp'; // Temporary directory path
    const PATH_LOGS             = '/var/logs'; // PHP logs directory
    const PATH_CACHE            = '/var/cache'; // PHP logs directory
    const PATH_CONTROLLERS      = '/controllers'; // Controllers' directory
	const PATH_MODELS           = '/models'; // Libraries path
	const PATH_LIBRARIES        = '/libraries'; // Libraries path
    const PATH_LOCALES          = '/locales'; // Languages' files directory
	const PATH_TEMPLATES        = '/templates'; // Template's directory path
    const PATH_FILES            = '/files'; // Files' directory
    const PATH_TMP_FILES        = '/files/tmp'; // Temporary files' directory
    
    /*
     * Once we have pathes, we can call essential libraries.
     * But you first need to have a settings.php file. 
     * Thereof contains all required constants to have functionnal libraries. 
     * Without it, some troubles may appear.
    **/
    if(file_exists(SYSTEM_ROOT.PATH_VAR.'/settings.php')):
        
        /**
         * All right ! Settings file exists.
         * We can load settings and required libraries
        **/
        require_once SYSTEM_ROOT.PATH_VAR . '/settings.php'; // Framework settings
        require_once SYSTEM_ROOT.PATH_CORE . '/compatibility.php'; // PHP compatibility functions
        require_once SYSTEM_ROOT.PATH_CORE . '/utilities.php'; // Framework functions
        
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
         * Autoload libraries
         * Core, Controllers, Models and external libraries
        **/
        spl_autoload_register(function($name) {
            
            $path = strtolower(str_replace('\\', DIRECTORY_SEPARATOR, $name));
            
            /**
             * Core libraries
            **/
            $name = strtolower($name);
            if(file_exists(SYSTEM_ROOT.PATH_CORE . "/$name.php"))
                require_once SYSTEM_ROOT.PATH_CORE . "/$name.php";
            
            /**
             * Controllers
            **/
            $controller = str_replace('controllers/', '', $path);
            if(file_exists(SYSTEM_ROOT.PATH_CONTROLLERS . "/$controller.ctrl.php"))
                require_once SYSTEM_ROOT.PATH_CONTROLLERS . "/$controller.ctrl.php";
            
            /**
             * Models
            **/
            $model = str_replace('models/', '', $path);
            if(file_exists(SYSTEM_ROOT.PATH_MODELS . "/$model.mdl.php"))
                require_once SYSTEM_ROOT.PATH_MODELS . "/$model.mdl.php";
            
            /**
             * External libraries
            **/            
            if(file_exists(SYSTEM_ROOT.PATH_LIBRARIES . "/$path.class.php"))
                require_once SYSTEM_ROOT.PATH_LIBRARIES . "/$path.class.php";
        });

        /**
         * Particular cases which require some adjustements or conditions
        **/
        if(!function_exists('json_decode') && !function_exists('json_encode'))
			require_once SYSTEM_ROOT.PATH_CORE . "/json.php"; // JSON functions
        
        
        /**
         * Start handling errors
        **/
        Console::handle();
        

        /**
         * Once everything is fine loaded, we call the options file.
         * This one will be used to add your own stuffs.
        **/
        if(file_exists(SYSTEM_ROOT.PATH_VAR.'/options.php'))
			require_once(SYSTEM_ROOT.PATH_VAR.'/options.php');
        
    endif;

    /**
     * That's it !
    **/

?>