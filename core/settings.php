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
	define('SESSION_CRYPT', 'e0266047bd12e95e0b410581a9a8a748eb8952e5'); // sha1(uniqid('sess_', true));
	define('TOKEN_SALT', 'f27e108aa1b1a92e8b7c2ac6437aa3a1ec4097ee'); // sha1(uniqid('tok_', true));
	define('COOKIE_CRYPT', 'b873ae4dcfb7e109b949d39c68bec689eab9572f');  // sha1(uniqid('cook_', true));
	
?>