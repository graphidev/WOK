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
    define('SYSTEM_DEFAULT_PROTOCOL', 'http://'); // System default protocol
    define('SERVER_DOMAIN', 'localhost'); // Server domain name or IP address
    define('SYSTEM_DIRECTORY_PATH', '/wok'); // Relative system directory path
	define('SYSTEM_ADDR', SERVER_DOMAIN.SYSTEM_DIRECTORY_PATH); // System address
	define('SYSTEM_TIMEZONE', 'Europe/Paris'); // System timezone
    define('SYSTEM_ACCEPT_LANGUAGES', 'en_EN,fr_FR'); // System accepted languages


    /**
     *	Security constants
     * You can use it to add salt in sessions, tokens or cookies' name/value
    **/
	define('SESSION_CRYPT', '7301051d0a086faa578a681cd1a75c3543d9fe2a'); // sha1(uniqid('sess_', true));
	define('TOKEN_SALT', '116692cbafdd5fba495bdb3526b7d8743ec73876'); // sha1(uniqid('tok_', true));
	define('COOKIE_CRYPT', 'c009b3c7d702beb35a944ab4d9cc42712658bb2a');  // sha1(uniqid('cook_', true));
	
?>