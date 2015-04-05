<?php

	/**
	* Web Operational Kit
	* The neither huger no micro extensible framework
	*
	* @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
	* @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
	* @license     BSD <license.txt>
	**/

    /**
     *    Welcome in WOK initialize file
     *
     * You don't have to edit any line of this file.
     * To define some settings, please have a look to settings.php
     * To add / call some options, please use options.php
	 *
    **/

	const WOK_MAJOR_VERSION        = 2; // Major version
	const WOK_MINOR_VERSION        = 0; // Minor version
	const WOK_RELEASE_VERSION      = 0; // Release version
	const WOK_EXTRA_RELEASE        = 'prototype'; // Extra version

    // Define full WOK version (without extra release)
	define('WOK_VERSION', WOK_MAJOR_VERSION.'.'.WOK_MINOR_VERSION.':'.WOK_RELEASE_VERSION);


    /*
     * The following lines will define default path of essential tools.
     * We suggest you to let them as they are for a better compatibility.
    **/

    // Define absolute project root path
	define('SYSTEM_ROOT', dirname(__DIR__));

    const PATH_CORE             = '/framework'; 		// Framework core's path
	const PATH_PACKAGES 		= '/packages';			// Packages folder's path
	const PATH_VAR              = '/var'; 				// Application configuration path
	const PATH_STORAGE        	= '/storage'; 			// Storage folder's path

	const PATH_TMP        		= '/storage/tmp'; 		// Temporary files' directory
	const PATH_MEDIA            = '/storage/media'; 			// Files' directory



    /**
     * Set UTF-8 as internal encoding
     * and as response encoding
    **/
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');


    /*
     * Once we have pathes, we can call essential libraries.
     * But you first need to have a settings.php file.
     * Thereof contains all required constants to have functionnal libraries.
     * Without it, some troubles may appear.
    **/
    if(file_exists(SYSTEM_ROOT.PATH_VAR.'/settings.php')):

        /**
         * All right ! Settings file exists.
         * We can load settings and basic libraries
        **/
        require_once SYSTEM_ROOT.PATH_VAR . '/settings.php'; // Framework settings
        require_once SYSTEM_ROOT.PATH_CORE . '/utils/helpers.php'; // Framework helpers

        /**
         * Initialize session
         * Also define a custom session name for compatibility
        **/
        session_name(SESSION_NAME);
        session_start();

        /**
         * Autoload classes by default
         * Libraries pathes uses namespaces
        **/
        spl_autoload_register(function($name) {

            $path = strtolower(str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $name));

			if(substr($path, 0, 11) == 'controllers')
				$path .= '.ctrl';

			elseif(substr($path, 0, 6) == 'models')
				$path .= '.mdl';

			elseif(substr($path, 0, 9) != 'framework')
				$path .= '.class';


			if(file_exists($class = SYSTEM_ROOT.'/'.$path.'.php'))
				require_once $class;

        });

        /**
         * Once everything is fine loaded, we call the options file.
         * This one will be used to add your own stuffs.
        **/
        if(file_exists($options = SYSTEM_ROOT.PATH_VAR.'/options.php'))
			require_once $options;

		/**
		 * Prevent not loaded settings
		 * This can be catched by including script
		**/
		return true;

    endif;

	/**
	 * If settings could not be loaded, return false.
	 * This allow to prevent bad usage of the init file.
	**/
	return false;

    /**
     * That's it !
    **/

?>
