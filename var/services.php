<?php

    /**
     * This file is destinated to contains your app services.
     * It has to return the service collection object.
    **/

    /**
     * Initialize Services collection
    **/
    $services = new Framework\Core\Services;

    
    /**
     * Autoloader
    **/
    $loader = new \Framework\Services\Loader;
    $loader->setNamespace('\Whoops', '/packages/whoops');
    $services->register('loader', $loader);


    /**
     * Framework locales
    **/
    $services->register('locales', new Framework\Services\Locales('fr_FR') );



    /**
     * Database tool
    **/
    $services->register('database',
        new Framework\Services\Database('mysql:dbname=wordwrap;host=localhost', 'root', '', array())
    );


    /**
     * Runtime logs
     * Set error reporting level
     * and shutdown callback
    **/
    $console = new Framework\Services\Console( E_ALL );
    $console->shutdown(function($errors) {

        $mail = new Framework\Services\Mail();
        $mail->setObject('['.SYSTEM_DOMAIN.'] Fatal error(s)');
        $mail->setFrom($_SERVER['SERVER_ADMIN'], 'Bug tracker');
        $mail->addTo($_SERVER['SERVER_ADMIN']);
        $mail->setBody(implode(PHP_EOL, $errors), Framework\Services\Mail::FORMAT_TEXT);
        $mail->send();

        if((!isset($_SERVER['SERVER_SOFTWARE']) && (PHP_SAPI == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)))) {

            $error = $errors[count($errors)-1];

            return Framework\Core\Response::text(
                'The application have been shutdown due to an error : ' . PHP_EOL
                . $error['message'] .' | '. $error['file'] .' : '. $error['line']
            )->render();

        } else {
            return Framework\Core\Response::view('error', 503)->assign(array(
                'errors' => $errors
            ))->render();
        }

    });

    // Register service
    $services->register('console', $console);

    // Forbidden PHP info file
    if(!SYSTEM_DEBUG && file_exists('phpinfo.php'))
        $console->log('File phpinfo.php have to be removed for security reasons', 'SECURITY');


    // Use Whoops manager (debug)
    if(SYSTEM_DEBUG && class_exists('\Whoops\Run')) {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }

    /**
     * System settings
    **/
    $services->register('settings', new Models\System\Settings(root('/var/settings.json')) );


    /**
     * Register events
    **/
    $services->register('events', require_once 'events.php');


    /**
     * Return the services collection
    **/
    return $services;
