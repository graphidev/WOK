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
	define('SESSION_CRYPT', '735cba040ff840dfb0d49b2b914b63671e15120d'); // sha1(uniqid('sess_', true));
	define('TOKEN_SALT', '2baff40ad4b9c021556a361e17af1062e4baac63'); // sha1(uniqid('tok_', true));
	define('COOKIE_CRYPT', '710a238e754f66e242a0fb0ac90d3897a665be85');  // sha1(uniqid('cook_', true));
	
?>