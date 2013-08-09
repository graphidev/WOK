<?php

	/**
     * Initialize WOK
    **/
	require_once 'core/init.php';


	/**
     * Define page url request
	**/
	$query = strip_system_root($_SERVER['REQUEST_URI']);
	$static = preg_replace('#(/[a-z0-9\.-]+)?(\?(.+))?$#iSU', "$1", $query);
	$additional = str_replace($static, '', preg_replace('#([a-z0-9/\.-]+)?(\?(.+))$#iSU', "$3", $query));	

	$GLOBALS['_GET']['REQUEST'] = str_replace(path(), '', path($static));
	$GLOBALS['_GET']['PARAMETERS'] = array();
	foreach(explode('&', $additional) as $i => $parameter) {
		@list($name, $value) = explode('=', $parameter);
		$GLOBALS['_GET']['PARAMETERS'][$name] = urldecode($value);
	}


	if(isset($_POST)):
		$GLOBALS['_POST'] = $_POST;
	endif;

    /**
     * Init session and locales languages
    **/
    $GLOBALS['session'] = new Session();
    $GLOBALS['LOCALES'] = new Locales($session->language());
    
    /**
     * Init libraries list
    **/
    
    
	require_once SYSTEM_ROOT.PATH_CORE . '/templates.php';
	
    /**
     * Preload
    **/
    if(file_exists(root('/preload.php')))
        require_once(root('/preload.php'));
    
    /**
     * Custom controller
    **/
    if(function_exists('controller') && controller()): 

	/**
     * Statics pages and API calls
	**/
	elseif(!empty($GLOBALS['_GET']['REQUEST'])):
    
        /**
         * API calls
        **/
        if(preg_match('#^api/(.+)$#', $GLOBALS['_GET']['REQUEST'])):

            /**
             * Directory, check the file with the same name in this directory
            **/
            if(is_dir(root(PATH_APIS.'/'.substr($GLOBALS['_GET']['REQUEST'], 0, -1)))):
                 
                $dirname = dirname($GLOBALS['_GET']['REQUEST']);
    
                if(substr($GLOBALS['_GET']['REQUEST'], -1) == '/'):
                     $filename = str_replace($dirname, '', substr($GLOBALS['_GET']['REQUEST'], 0, -1));
                else:
                    $filename = str_replace($dirname, '', $GLOBALS['_GET']['REQUEST']);
                endif;
                
                $api = root(PATH_APIS.'/'.str_replace('//', '/', $GLOBALS['_GET']['REQUEST'].$filename));

                if(file_exists($api))
                    include($api);
                else
                    tpl_static_page('404');
            
            /**
             * File, check the filename
            **/
            else:
            
                $api = root(PATH_APIS.'/'.$GLOBALS['_GET']['REQUEST']);
                if(file_exists($api))
                    include($api);
                else
                    tpl_static_page('404');
                    
    
            endif;
        
        /**
         * Statics pages
        **/
        else:
            
            /**
             * Directory, check the file with the same name in this directory
            **/
            if(is_dir(root(PATH_TEMPLATES.'/'.substr($GLOBALS['_GET']['REQUEST'], 0, -1)))):
                 
                $dirname = dirname($GLOBALS['_GET']['REQUEST']);
    
                if(substr($GLOBALS['_GET']['REQUEST'], -1) == '/'):
                     $filename = str_replace($dirname, '', substr($GLOBALS['_GET']['REQUEST'], 0, -1));
                else:
                    $filename = str_replace($dirname, '', $GLOBALS['_GET']['REQUEST']);
                endif;
            
                tpl_static_page(str_replace('//', '/', $GLOBALS['_GET']['REQUEST'].$filename));
            
            /**
             * File, check the filename
            **/
            else:
    
                tpl_static_page($GLOBALS['_GET']['REQUEST']);
    
            endif;

        endif;
    
	/**
     * Homepage
	**/
	elseif(empty($GLOBALS['_GET']['REQUEST'])):
		tpl_static_page('homepage');
	else:
		tpl_static_page($GLOBALS['_GET']['REQUEST']);
	endif;
	
?>
