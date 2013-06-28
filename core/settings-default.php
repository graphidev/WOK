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
    define('SYSTEM_DIRECTORY_PATH', '/wok-directory'); // Relative system directory path
	define('SYSTEM_ADDR', SERVER_DOMAIN.SYSTEM_DIRECTORY_PATH); // System address
	define('SYSTEM_TIMEZONE', 'Europe/Paris'); // System timezone


    /**
     *	Security constants
     * You can use it to add salt in sessions, tokens or cookies' name/value
    **/
	define('SESSION_CRYPT', 'a1b2c3'); // sha1(uniqid('sess_', true));
	define('TOKEN_SALT', 'd4e5f6'); // sha1(uniqid('tok_', true));
	define('COOKIE_CRYPT', 'g7h8i9');  // sha1(uniqid('cook_', true));
	
?>