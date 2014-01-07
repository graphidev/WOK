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
    const SYSTEM_LANGUAGES              = 'en_EN fr_FR en_US'; // System accepted languages (separate with space)
    const SYSTEM_DEFAULT_LANGUAGE       = 'en_EN'; // System default language


    /**
     * Console settings (logs)
    **/
    const CONSOLE_LOG_FORMAT            = '[:time] [:type] :message'; // Can contains : time, type, message, file, line
    const CONSOLE_FATAL_EMAILS          = 'debug@domain.tld'; // Separate e-mails with spaces
    const CONSOLE_HANDLER_LEVEL         = E_ALL; // Errors that must be handled (default: E_ALL, false to disallow handling)
    

    /**
     * Salts and keys
     * Generated randomly on setup for security reasons
    **/
    const TOKENS_SALT                   = '069de552f000c9b477ad'; // Tokens salt
    const COOKIES_SALT                  = '7b0dff4c1df29468f29c';  // Cookies Encryption key

?>