<?php

    /**
     * The options file exists to call
     * some custom scripts that will be executed
     * on framework start.
     *
     * You can use it to redefine some server configuration
     * or custom settings for your app
    **/

    /**
     * Force system timezone
    **/
    @date_default_timezone_set('Europe/Paris');

    /**
     * Allow zlib compression
     * This should be made in php.ini !
    **/
    ini_set("zlib.output_compression", "On");


    /**
     * Composer libraries autoloader
    **/
    if(file_exists($file = SYSTEM_ROOT.'/composer.json') ) {
        $composer = json_decode(file_get_contents($file), true);
    }

    $directory = (!empty($composer['config']['vendor-dir']) ? $composer['config']['vendor-dir'] : 'vendor');

    if(file_exists($autoloader = SYSTEM_ROOT."/$directory/autoload.php"))
        require_once $autoloader;
