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
    const SYSTEM_LANGUAGES              = 'en_EN fr_FR en_US'; // System accepted languages (separate with commas)
    const SYSTEM_DEFAULT_LANGUAGE       = 'en_EN'; // System default language
    
    /**
     * Templates settings
    **/
    const TEMPLATES_CACHE_STATICS       = false; // Cache static pages by default (true/false)
    const TEMPLATES_CACHE_TIME          = 300 ; // Max cache files lifetime (default: 5 minutes)


    /**
     * Console settings (logs)
    **/
    const CONSOLE_LOG_FORMAT            = '[:time] [:type] :message'; // Can contains : time, type, message, file, line
    const CONSOLE_FATAL_EMAILS          = 'debug@domain.tld'; // Separate e-mails with spaces
    const CONSOLE_HANDLER_LEVEL         = E_ALL; // Errors that must be handled (default: E_ALL, false to disallow handling)
    

    /**
     * Tokens configuration
    **/
    const TOKENS_LIFETIME           = 300; // Max tokens lifetime (default: 5 minutes [18000])
    const TOKENS_SALT               = '069de552f000c9b477ad'; // Tokens key


    /**
     * Sessions configuration
    **/
    const SESSIONS_LIFETIME         = 1296000; // Max sessions lifetime (default: 15 days [77760000])
    const SESSIONS_SALT             = '68dae0ab4687006c8c50'; // sessions key


    /**
     * Cookies configuration
    **/
    const COOKIES_LIFETIME              = 13392000; // Max cookies lifetime (default: 6 months [964224000])
    const COOKIES_SALT                  = '7b0dff4c1df29468f29c';  // Encryption key
    const COOKIES_CRYPT_MODE            = MCRYPT_MODE_CBC; // Encription mode
    const COOKIES_CRYPT_ALGORITHM       = MCRYPT_RIJNDAEL_256; // Encription algorithm	

?>