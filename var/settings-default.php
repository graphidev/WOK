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
    const SYSTEM_DOMAIN                 = 'domain.tld'; // Server domain name or IP address
    const SYSTEM_DOMAIN_ALIAS           = ''; // Server alias domains (separate with spaces)
    const SYSTEM_DIRECTORY              = '/'; // Relative system directory path

	const SYSTEM_TIMEZONE               = 'Europe/Paris'; // System timezone
    const SYSTEM_LANGUAGES              = 'en_EN'; // System accepted languages (separate with commas)
    const SYSTEM_DEFAULT_LANGUAGE       = 'en_EN'; // System default language
    

    /**
     * Console settings (logs)
    **/
    const CONSOLE_LOG_FORMAT            = '[:time] [:type] :message'; // Can contains : time, type, message, file, line
    const CONSOLE_FATAL_EMAILS          = 'debug@domain.tld'; // Separate e-mails with spaces
    const CONSOLE_HANDLER_LEVEL         = false; // Errors that must be handled (default: E_ALL, false to disallow handling)
    

    /**
     * Tokens configuration
    **/
    const TOKENS_LIFETIME           = 300; // Max tokens lifetime (default: 5 minutes [18000])
    const TOKENS_SALT               = '0c3188325fe063253c55'; // Tokens key


    /**
     * Sessions configuration
    **/
    const SESSIONS_LIFETIME         = 1296000; // Max sessions lifetime (default: 15 days [77760000])
    const SESSIONS_SALT             = 'a059df113b5895fc7106'; // sessions key


    /**
     * Cookies configuration
    **/
    const COOKIES_LIFETIME              = 13392000; // Max cookies lifetime (default: 6 months [964224000])
    const COOKIES_SALT                  = '1b44fd3dc1b69ecc4db8';  // Encryption key
    const COOKIES_CRYPT_MODE            = MCRYPT_MODE_CBC; // Encription mode
    const COOKIES_CRYPT_ALGORITHM       = MCRYPT_RIJNDAEL_256; // Encription algorithm

?>