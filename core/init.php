<?php 
    /**
     *    Welcome in WOK initialize file
     *
     * You don't have to edit any line of this file.
     * To define some settings, please have a look to settings.php
     * To add / call some options, please use options.php
     *
    **/
    
	const WOK_MAJOR_VERSION        = 1; // Major version
	const WOK_MINOR_VERSION        = 3; // Minor version
	const WOK_RELEASE_VERSION      = 0; // Release version
	const WOK_EXTRA_RELEASE        = 'prototype'; // Extra version
    
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
        require_once SYSTEM_ROOT.PATH_CORE . '/utf8.php'; // UTF-8 compatible functions
        require_once SYSTEM_ROOT.PATH_CORE . '/helpers.php'; // Framework helpers     

        /**
         * Initialize session
         * Also define a custom session name for compatibility
        **/
        session_name(SESSION_NAME);
        session_start();

        /**
         * Define system timezone
        **/
        @date_default_timezone_set(SYSTEM_TIMEZONE);

        /*
         * Define default system language
         * Also initialize default locale
        */
        define('SYSTEM_DEFAULT_LANGUAGE', (strpos(SYSTEM_LANGUAGES, ' ') === false ? SYSTEM_LANGUAGES : strstr(SYSTEM_LANGUAGES, ' ', true)));
        setLocale(LC_ALL, SYSTEM_DEFAULT_LANGUAGE.'.UTF-8');
    
        /**
         * Autoload libraries
         * Core, Controllers, Models and external libraries
        **/
        spl_autoload_register(function($name) {
            
            $path = strtolower(str_replace('\\', DIRECTORY_SEPARATOR, $name));
            
            /**
             * Exceptions
            **/
            if(substr($name, -9) == 'Exception')
                require_once SYSTEM_ROOT.PATH_CORE . '/exceptions.php';
            
            /**
             * Core libraries
            **/
            $class = strtolower($name);
            if(file_exists($class = SYSTEM_ROOT.PATH_CORE . "/$class.php"))
                require_once $class;
            
            /**
             * Controllers
            **/
            $controller = str_replace('controllers/', '', $path);
            if(file_exists($controller = SYSTEM_ROOT.PATH_CONTROLLERS . "/$controller.ctrl.php"))
                require_once $controller;
            
            /**
             * Models
            **/
            $model = str_replace('models/', '', $path);
            if(file_exists($model = SYSTEM_ROOT.PATH_MODELS . "/$model.mdl.php"))
                require_once $model;
            
            /**
             * External libraries
            **/            
            if(file_exists($library = SYSTEM_ROOT.PATH_LIBRARIES . "/$path.class.php"))
                require_once $library;
            
        });

        /**
         * Once everything is fine loaded, we call the options file.
         * This one will be used to add your own stuffs.
        **/
        if(file_exists($options = SYSTEM_ROOT.PATH_VAR.'/options.php'))
			require_once $options;
        
    endif;

    /**
     * That's it !
    **/

?>