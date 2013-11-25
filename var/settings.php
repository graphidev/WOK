<?php 
/**
 *
 *	This file contains all the system settings. This parameters are defined at the setup step.
 *	
 *	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**
 *		/!\ Changing these parameters may cause permanent damage and malfunction /!\
 *	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**
 *
**/


    /**
     * Sytem informations
     * This informations are required for some functions and other conditions.
    **/
    const SYSTEM_DEFAULT_PROTOCOL           = 'http://'; // System default protocol

    const SERVER_DOMAIN                     = 'localhost'; // Server domain name or IP address
    const SERVER_DOMAIN_ALIAS               = '172.0.0.1 wok.loc'; // Server alias domains (separate with spaces)
    const SYSTEM_DIRECTORY_PATH             = '/wok'; // Relative system directory path
	define('SYSTEM_ADDR', SERVER_DOMAIN.SYSTEM_DIRECTORY_PATH); // System address
    
	const SYSTEM_TIMEZONE                   = 'Europe/Paris'; // System timezone
    const SYSTEM_ACCEPT_LANGUAGES           = 'en_EN'; // System accepted languages (separate with commas)
    $languages = explode(',', SYSTEM_ACCEPT_LANGUAGES);
    define('SYSTEM_DEFAULT_LANGUAGE', $languages[0]); // System default language (calculated)
    
    /**
     * Cookies & session
    **/
    const MAX_COOKIES_LIFETIME          = 15552000; // Max cookies life time

    /**
     * Console settings (logs)
    **/
    const CONSOLE_LOG_FORMAT            = '[:time] [:type] :message'; // Can contains : time, type, message, file, line
    const CONSOLE_FATAL_EMAILS          = 'sebastien@graphidev.fr'; // Separate e-mails with spaces
    const CONSOLE_HANDLER_LEVEL         = E_ALL; // Errors that must be handled (default: E_ALL, false to disallow handling)

    /**
     *	Security constants
     * You can use it to add salt in sessions, tokens or cookies' name/value
    **/
    const SESSION_SALT             = 'b4323b6e3dab3c5749bbfe758ace614a8f7dbae3'; // sha1(uniqid('sess_', true));
    const TOKEN_SALT               = '2c0dd3409715c28902f9ba7ef88900f17c25250c'; // sha1(uniqid('tok_', true));
    const COOKIE_SALT              = '065805728931e99b11a6e9f03f9bdbf9956bdc88';  // sha1(uniqid('cook_', true));
	
?>