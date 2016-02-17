<?php

    /**
     * Application services configuration file
     *
     * /!\ WARNING /!\
     * Some configuration values may be used in your
     * application scripts. Take care to not erase
     * or update some predefined values
    **/

    return function($settings) {

        /**
         * Initialize Services collection
         * Register settings as primary
        **/
        $services = new Application\Services;
        $services->register('settings', $settings);


        /**
         * Application loader
        **/
        $loader = new Application\Loader;

            // Components path
            $loader->addPrefix('Components', function($path) {
                $path  = mb_strtolower($match = 'Components') . mb_substr($path,  mb_strlen($match));
                return APPLICATION_ROOT .'/'. mb_str_replace('\\', DIRECTORY_SEPARATOR, $path);
            });

            // Models path
            $loader->addPrefix('Models', function($path) {
                $path  = mb_strtolower($match = 'Models'). mb_substr($path,  mb_strlen($match));
                return APPLICATION_ROOT .'/'.mb_str_replace('\\', DIRECTORY_SEPARATOR, $path);
            });

            // Controllers path
            $loader->addPrefix('Controllers', function($path) {
                $path  = mb_strtolower($match = 'Controllers'). mb_substr($path,  mb_strlen($match));
                return APPLICATION_ROOT .'/'. mb_str_replace('\\', DIRECTORY_SEPARATOR, $path);
            });

            // OAuth client
            $loader->addPath('tmhOAuth', APPLICATION_ROOT.'/vendor/themattharris/tmhoauth/tmhOAuth.php');


        // Register loader as a service
        $services->register('loader', $loader);


        /**
         * Application locales
        **/
        $services->register('locales', function($locale) {

            return new Locales\Locales(
                root(PATH_STORAGE.'/locales'), $locale
            );

        });


        /**
         * Console logs
        **/
        $console = new Console\Console(
            new Console\Adapters\Files(root('/tmp/logs/runtime'))
        );

        // Define reporting level
        error_reporting(E_ALL);

        // Not catched exception handler : throw an error
        set_exception_handler(function($e) {

            $error = 'Not catched exception '.get_class($e).' width message : "'.$e->getMessage().'" | '.$e->getFile(). ' '.$e->getLine();
            trigger_error($error, E_USER_ERROR);

        });

        // Errors handler
        set_error_handler(function($type, $message, $file, $line) use($console)  {

            if(!(error_reporting() & $type)) return;

            switch ($type) {
                case E_USER_ERROR:
                    $stype = 'ERROR';
                    break;

                case E_USER_WARNING:
                    $stype = 'WARNING';
                    break;

                case E_USER_NOTICE:
                    $stype = 'NOTICE';
                    break;

                case E_USER_DEPRECATED:
                    $stype = 'DEPRECATED';
                    break;

                default:
                   $stype = (is_string($type) ? mb_strtoupper($type) : 'ERROR');
            }



            $backtrace = new Console\Components\Backtrace(2,
                Console\Components\Backtrace::IGNORE_CLOSURES | Console\Components\Backtrace::IGNORE_TRIGGERED_ERRORS
            );

            // Register error as log
            $console->log('['.$type.'] '.$message, $backtrace);
            if($type == E_USER_ERROR) exit;

            // Prevent built-in behavior
            return true;

        });

        $services->register('console', $console);


        /**
         * Shutdown response
        **/
        register_shutdown_function(function() {

            if(!error_get_last())
                return;


        });


        /**
         * Twitter OAuth client
         * Settings are enabled in the settings file
        **/
        $services->register('TwitterOAuthClient', function() use($settings) {
            return new tmhOAuth($settings->services->twitter);
        });


        /**
         * Instanciate Whoops error manager on debug mode
        **/
        if($settings->environment->debug) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }

        /**
         * Return services manager
        **/
        return $services;

    };
