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
    const SERVER_DOMAIN                     = 'wok.loc'; // Server domain name or IP address
    const SYSTEM_DIRECTORY_PATH             = '/'; // Relative system directory path
	define('SYSTEM_ADDR', SERVER_DOMAIN.SYSTEM_DIRECTORY_PATH); // System address
    
	const SYSTEM_TIMEZONE                   = 'Europe/Paris'; // System timezone
    const SYSTEM_ACCEPT_LANGUAGES           = 'fr_FR'; // System accepted languages
    $languages = explode(',', SYSTEM_ACCEPT_LANGUAGES);
    define('SYSTEM_DEFAULT_LANGUAGE', $languages[0]); // System default language (calculated)

    /**
     *	Security constants
     * You can use it to add salt in sessions, tokens or cookies' name/value
    **/
    const SESSION_CRYPT             = 'e574ff35e03bd263b27a1b17374fcec06872b3fa'; // sha1(uniqid('sess_', true));
    const TOKEN_SALT                = '186bca2f948a5658f9c2abbcc6601e2b99b33797'; // sha1(uniqid('tok_', true));
    const COOKIE_CRYPT              = '09a72ddfbdf4b9f00e1de79202f99136cba5e381';  // sha1(uniqid('cook_', true));
	
?>