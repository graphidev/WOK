<?php 
/**
 *
 *	This file contains all the system settings. This parameters are defined at the setup step.
 *	
 *	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**
 *		/!\ Changing these parameters may cause permanent damages and malfunctions /!\
 *	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**
 *
**/


    /**
     * Sytem informations
     * These informations are required in most of core classes
    **/
    const SYSTEM_PROTOCOL               = 'http://'; // System default protocol
    const SYSTEM_DOMAIN                 = 'localhost'; // Server domain name or IP address
    const SYSTEM_DOMAIN_ALIAS           = ''; // Server alias domains (separate with spaces)
    const SYSTEM_DIRECTORY              = '/wok'; // Relative system directory path

	const SYSTEM_TIMEZONE               = 'Europe/Paris'; // System timezone
    const SYSTEM_LANGUAGES              = ''; // System accepted languages (separate with commas)
    const SYSTEM_DEFAULT_LANGUAGES      = 'en_EN'; // System default language
    

    /**
     * Console settings (logs)
    **/
    const CONSOLE_LOG_FORMAT            = '[:time] [:type] :message'; // Can contains : time, type, message, file, line
    const CONSOLE_FATAL_EMAILS          = 'debug@domain.tld'; // Separate e-mails with spaces
    const CONSOLE_HANDLER_LEVEL         = E_ALL; // Errors that must be handled (default: E_ALL, false to disallow handling)
    

    /**
     * Tokens configuration
    **/
    const TOKENS_LIFETIME           = 18000; // Max cookies lifetime (default: 5 minutes [18000])
    const TOKENS_SALT               = '2c0dd3409715c28902f9ba7ef88900f17c25250c'; // sha1(uniqid('tok_', true)); 


    /**
     * Sessions configuration
    **/
    const SESSIONS_LIFETIME         = 77760000; // Max sessions lifetime (default: 15 days [77760000])
    const SESSIONS_SALT             = 'b4323b6e3dab3c5749bbfe758ace614a8f7dbae3'; // sha1(uniqid('sess_', true));


    /**
     * Cookies configuration
    **/
    const COOKIES_LIFETIME          = 964224000; // Max cookies lifetime (default: 6 months [964224000])
    const COOKIES_SALT              = '065805728931e99b11a6e9f03f9bdbf9956bdc88';  // sha1(uniqid('cook_', true));
	

?>