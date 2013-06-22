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
	MySQL database log informations
	Please be carreful with these settings. A mistake will shut off the system.
*/
	define('SQL_HOSTNAME', 'localhost'); // Database hostname (default: localhost)
	define('SQL_USERNAME', 'root'); // Database username (default: root)
	define('SQL_PASSWORD', ''); // Database password
	define('SQL_DATABASE', 'graphidev'); // Database name
	define('SQL_ENCODE', 'UTF8'); // Database data encode
	define('SQL_INTERFACE', 'PDO'); // Database interface (PDO/MySQLib)
	define('SQL_TABLES_PREFIX', ''); // Database system tables prefixs

/**
	Security sessions & cookies parameters.
*/
	define('SESSION_CRYPT', '4f965f7d5579b');
	define('TOKEN_SALT', '4f965f7d5579b');
	define('COOKIE_CRYPT', '4f965f7d5579b');

/**
	Sytem informations.
*/
	define('SITE_ADDR', 'http://localhost/graphidev'); // System address
	define('SITE_URL_REWRITING', true); // Allow URL Rewriting
	define('SYSTEM_DEFAULT_LANGUAGE', 'fr'); // Default system language
	define('SYSTEM_IS_UPDATING', false); // Is system updating
	define('SYSTEM_ADMIN_CONTACT', 'contact@graphidev.fr'); // System admin contact
	
	define('IS_ACTIVATED_ANALYTICS', false); // Internal analytics system
	define('SYSTEM_ANALYTICS_API', 'Google Analytics'); // Analytics system (default/Google Analytics)
	define('GOOGLE_ANALYTICS_ID', 'UA-37956333-1'); // If SYSTEM_ANALYTICS_API = Google Analytics, need GOOGLE_ANALYTICS_ID
	define('SYSTEM_TIMEZONE', 'Europe/Paris'); // System timezone
	define('SYSTEM_DEFAULT_DATETIME', 'Y-m-d H:i:s'); // Default date format
	
?>