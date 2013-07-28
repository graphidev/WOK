<?php

	/**
     * Initialize WOK
    **/
	require_once "core/init.php";


	/**
     * Define page url request
	**/
	$query = strip_system_root($_SERVER['REQUEST_URI']);
	$static = preg_replace('#(/[a-z0-9\.-]+)?(\?(.+))?$#iSU', "$1", $query);
	$additional = str_replace($static, '', preg_replace('#([a-z0-9/\.-]+)?(\?(.+))$#iSU', "$3", $query));	

	define('url', path($static));

	$GLOBALS['_GET']['REQUEST'] = str_replace(path(), '', url);
	$GLOBALS['_GET']['PARAMETERS'] = array();
	foreach(explode('&', $additional) as $i => $parameter) {
		@list($name, $value) = explode('=', $parameter);
		$GLOBALS['_GET']['PARAMETERS'][$name] = urldecode($value);
	}


	if(isset($_POST)):
		$GLOBALS['_POST'] = $_POST;
	endif;

    $GLOBALS['session'] = new Session();
    $GLOBALS['LOCALES'] = new Locales($session->language());
    
	require_once SYSTEM_ROOT.PATH_CORE."/templates.php";
	
    /**
     * Preload
    **/
    if(file_exists(root('preload.php')))
        require_once(root('preload.php'));

    /**
     * Special views
    **/
    if(function_exists('controller') && controller()): 

	/**
     * Statics pages
	**/
	elseif(!empty($GLOBALS['_GET']['REQUEST'])):

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
    
	/**
     * Homepage
	**/
	elseif(empty($GLOBALS['_GET']['REQUEST'])):
		tpl_static_page('homepage');
	else:
		tpl_static_page($GLOBALS['_GET']['REQUEST']);
	endif;
	
?>
