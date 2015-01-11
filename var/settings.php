<?php 
/**
 *
 *	This file contains all the system settings. This parameters are defined at the setup step.
 *	
 *	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**
 *		/!\ Changing these parameters may cause permanent damages and malfunctions /!\
 *	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**
 *
 * @package System/Configuration
**/


    /**
     * Sytem informations
     * These informations are required in most of core classes
	 *
	 * @package System
    **/
    const SYSTEM_PROTOCOL               = 'http'; // System default protocol
    const SYSTEM_DOMAIN                 = 'localhost'; // Server domain name or IP address
    const SYSTEM_DIRECTORY              = '/wok'; // Relative system directory path

	const SYSTEM_TIMEZONE               = 'Europe/Paris'; // System timezone
    const SYSTEM_LANGUAGES              = 'en_US'; // System accepted languages (separate with space)

    
    /**
     * Project environnement state
    **/
    const SYSTEM_DEBUG                  = true;
    const SYSTEM_MAINTENANCE            = false;


    /**
     * Salts and keys
     * Generated randomly on setup for security reasons
    **/
    const TOKENS_SALT                   = '5476bf8746bd0fe0322b'; // Tokens salt
    const COOKIES_SALT                  = 'fefc6da65ece4bd8b472';  // Cookies Encryption key
    const SESSION_NAME                  = '2a4e1b763944fcf79aaa';  // Session name
?>