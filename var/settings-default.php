<?php 
/**
 *
 *	This file contains all the system settings. This parameters are defined at the setup step.
 *	
 *	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**
 *		/!\ Changing these parameters may cause permanent damages and malfunctions /!\
 *	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**	**
 *
**/


    /**
     * Sytem informations
     * These informations are required in most of core classes
    **/
    const SYSTEM_PROTOCOL               = 'http'; // System default protocol
    const SYSTEM_DOMAIN                 = 'localhost'; // Server domain name or IP address
    const SYSTEM_DIRECTORY              = '/wok'; // Relative system directory path

	const SYSTEM_TIMEZONE               = 'Europe/Paris'; // System timezone
    const SYSTEM_LANGUAGES              = 'en_EN'; // System accepted languages (separate with space)

    
    /**
     * Project environnement state
    **/
    const SYSTEM_DEBUG                  = true;
    const SYSTEM_MAINTENANCE            = false;


    /**
     * Salts and keys
     * Generated randomly on setup for security reasons
    **/
    const TOKENS_SALT                   = '069de552f000c9b477ad'; // Tokens salt
    const COOKIES_SALT                  = '7b0dff4c1df29468f29c';  // Cookies Encryption key
    const SESSION_NAME                  = '2a4e1b763944fcf79aaa';  // Session name
?>