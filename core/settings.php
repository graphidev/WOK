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


    /**
     *	Security constants
     * You can use it to add salt in sessions, tokens or cookies' name/value
    **/
	define('SESSION_CRYPT', 'f2c0e7c87fce99ab60860894ae72829b6db13f63'); // sha1(uniqid('sess_', true));
	define('TOKEN_SALT', '20c47982b0bd5e5a70d7a1c1b6f23493e1b3e593'); // sha1(uniqid('tok_', true));
	define('COOKIE_CRYPT', 'cfe3523a88eb9f6faa429384ad38a32c7e415ade');  // sha1(uniqid('cook_', true));
	
?>