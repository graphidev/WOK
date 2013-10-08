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
    const SYSTEM_DIRECTORY_PATH             = ''; // Relative system directory path
	define('SYSTEM_ADDR', SERVER_DOMAIN.SYSTEM_DIRECTORY_PATH); // System address
    
	const SYSTEM_TIMEZONE                   = 'Europe/Paris'; // System timezone
    const SYSTEM_ACCEPT_LANGUAGES           = 'en_EN'; // System accepted languages
    $languages = explode(',', SYSTEM_ACCEPT_LANGUAGES);
    define('SYSTEM_DEFAULT_LANGUAGE', $languages[0]); // System default language (calculated)

    /**
     *	Security constants
     * You can use it to add salt in sessions, tokens or cookies' name/value
    **/
    const SESSION_CRYPT             = 'f94e7a3e4a909479f42efff836a6a955ddc83ecb'; // sha1(uniqid('sess_', true));
    const TOKEN_SALT                = 'b7d34494e4435ee2e54e2ea419726de0903ebfdb'; // sha1(uniqid('tok_', true));
    const COOKIE_CRYPT              = 'b932ee8624a872c8c505678e68e11cae17d5ecb5';  // sha1(uniqid('cook_', true));
	
?>