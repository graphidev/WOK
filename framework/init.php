<?php

    /**
    * Web Operational Kit
    * The neither huger nor micro humble framework
    *
    * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @license     BSD <license.txt>
    **/

    /**
     *    Welcome in the WOK initialization file
     *
     * You don't have to edit any line of this file.
     * To add / call some options, please use /var/options.php
     *
    **/
    const WOK_MAJOR_VERSION        = 2;         // Major version
    const WOK_MINOR_VERSION        = 0;         // Minor version
    const WOK_PATCH_VERSION        = 0;         // Patch version
    const WOK_EXTRA_RELEASE        = 'stable';   // Extra version
    const WOK_RELEASE_NAME         = 'Helium';  // Release name

    // Define full WOK version (without extra release)
    define('WOK_VERSION', WOK_MAJOR_VERSION.'.'.WOK_MINOR_VERSION.':'.WOK_PATCH_VERSION);


    /*
     * The following lines will define default paths of essential tools.
     * We suggest you to let them as they are for a better compatibility.
    **/
    const PATH_CORE             = '/framework';         // Framework core's path
    const PATH_STORAGE          = '/storage';           // Storage folder's path
    const PATH_TEMPLATES        = '/templates';         // Views template's path
    const PATH_VAR              = '/var';               // Application configuration path
    const PATH_TMP              = '/tmp';               // Temporary files' directory

    // Define absolute project root path
    define('APPLICATION_ROOT', dirname(__DIR__));

    /**
     * Set UTF-8 as internal encoding
     * and as response encoding
    **/
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');


    /*
     * Once we have pathes, we can require essential libraries.
     * Thereof contains all required constants to have functionnal libraries.
     * Without it, some troubles may appear.
    **/
    require_once APPLICATION_ROOT.PATH_CORE . '/helpers/supports.php';       // Compatibility functions
    require_once APPLICATION_ROOT.PATH_CORE . '/helpers/unicode.php';        // Unicode helpers
    require_once APPLICATION_ROOT.PATH_CORE . '/helpers/system.php';         // System helpers
    require_once APPLICATION_ROOT.PATH_CORE . '/helpers/variables.php';      // Variables helpers
    require_once APPLICATION_ROOT.PATH_CORE . '/helpers/strings.php';        // Strings helpers
    require_once APPLICATION_ROOT.PATH_CORE . '/helpers/arrays.php';         // Arrays helpers
    require_once APPLICATION_ROOT.PATH_CORE . '/helpers/helpers.php';        // Others helpers


    /**
     * Autoload classes by default
     * Libraries pathes uses namespaces
    **/
    spl_autoload_register(function($name) {

        $path = mb_str_replace('\\', DIRECTORY_SEPARATOR, $name);

        if(file_exists($class = APPLICATION_ROOT.PATH_CORE.'/'.$path.'.php'))
            require_once $class;

    });

    /**
     * Once everything is fine loaded, we call the options file.
     * This one will be used to add your own stuffs.
    **/
    if(file_exists($options = APPLICATION_ROOT.PATH_VAR.'/options.php'))
        require_once $options;

    /**
     * Prevent not loaded settings
     * This can be catched by including script
     * @note This is a previous versions compatibility stuff.
    **/
    return true;


    /**
     * That's it ! Let's play with WOK !
    **/
