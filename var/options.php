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
	if(file_exists($file = SYSTEM_ROOT.'/composer.json') ) {
		$composer = json_decode(file_get_contents($file), true);
	}

	$directory = (!empty($composer['config']['vendor-dir']) ? $composer['config']['vendor-dir'] : 'vendor');
	
	if(file_exists($autoloader = SYSTEM_ROOT."/$directory/autoload.php"))
		$loader = require_once $autoloader;
    
    /**
     * Allow zlib compression
     * This should be made in php.ini !
    **/
    ini_set("zlib.output_compression", "On");


?>