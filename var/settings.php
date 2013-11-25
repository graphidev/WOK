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
    const SYSTEM_LANGUAGES              = 'en_EN'; // System accepted languages (separate with commas)
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
    const TOKENS_SALT               = '9605db0b9613d7294811ce17b1abfefdb2661de6'; // sha1(uniqid('tok_', true)); 


    /**
     * Sessions configuration
    **/
    const SESSIONS_LIFETIME         = 77760000; // Max sessions lifetime (default: 15 days [77760000])
    const SESSIONS_SALT             = 'bcbab5b629248f5078c82efa04f1b1e955b0dd51'; // sha1(uniqid('sess_', true));


    /**
     * Cookies configuration
    **/
    const COOKIES_LIFETIME          = 964224000; // Max cookies lifetime (default: 6 months [964224000])
    const COOKIES_SALT              = '0a778af1988aa9861bba90be07e815dc22adb324';  // sha1(uniqid('cook_', true));
	

?>