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
    const SYSTEM_ACCEPT_LANGUAGES           = 'en_EN'; // System accepted languages
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
    const SESSION_CRYPT             = 'f94e7a3e4a909479f42efff836a6a955ddc83ecb'; // sha1(uniqid('sess_', true));
    const TOKEN_SALT                = 'b7d34494e4435ee2e54e2ea419726de0903ebfdb'; // sha1(uniqid('tok_', true));
    const COOKIE_CRYPT              = 'b932ee8624a872c8c505678e68e11cae17d5ecb5';  // sha1(uniqid('cook_', true));
	
?>