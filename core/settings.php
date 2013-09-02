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
    define('SERVER_DOMAIN', 'wok.loc'); // Server domain name or IP address
    define('SYSTEM_DIRECTORY_PATH', ''); // Relative system directory path

	define('SYSTEM_ADDR', SERVER_DOMAIN.SYSTEM_DIRECTORY_PATH); // System address
    
	const SYSTEM_TIMEZONE                   = 'Europe/Paris'; // System timezone
    const SYSTEM_ACCEPT_LANGUAGES           = 'en_EN,fr_FR'; // System accepted languages
    const SYSTEM_DEFAULT_LANGUAGE           = 'en_EN'; // System default language
    
    
    /**
     * System logs
    **/
    define('CONSOLE_LOGS_RECEIVER', 'sebasalex@free.fr');
    

    /**
     *	Security constants
     * You can use it to add salt in sessions, tokens or cookies' name/value
    **/
	define('SESSION_CRYPT', '1eca304156306396a85875ba9b96124335a5614a'); // sha1(uniqid('sess_', true));
	define('TOKEN_SALT', '1a61969b002ab8a3ee2a050ebd8d28c5a043a17b'); // sha1(uniqid('tok_', true));
	define('COOKIE_CRYPT', '3e400e8c954584628a156305cf79b9886a0fcafb');  // sha1(uniqid('cook_', true));
	
?>