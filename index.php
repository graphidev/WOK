<?php

	/**
     * Initialize WOK
    **/
	require_once "core/init.php";


	/**
     * Define page url request
	**/
	$query = strip_host_root($_SERVER['REQUEST_URI']);
	$static = preg_replace('#(/[a-z0-9\.-]+)?(\?(.+))?$#iSU', "$1", $query);
	$additional = str_replace($static, '', preg_replace('#(/[a-z0-9\.-]+)?(\?(.+))?$#iSU', "$3", $query));	
		
	define('url', path($static));
		
	$GLOBALS['_GET']['REQUEST'] = str_replace(SITE_ADDR.'/', '', url);
	$GLOBALS['_GET']['PARAMETERS'] = array();
	foreach(explode('&', $additional) as $i => $parameter) {
		@list($name, $value) = explode('=', $parameter);
		$GLOBALS['_GET']['PARAMETERS'][$name] = urldecode($value);
	}
	
	if(isset($_POST)):
		$GLOBALS['_POST'] = $_POST;
	endif;	

		
	/**
     * Launch statistics engine
	**/
	if(IS_ACTIVATED_ANALYTICS):
		$stats = new statistics();
		$stats->register();
	endif;
	
    
	load_core_library('templates');
	
	/**
     * Statics pages
	**/
	if(!empty($GLOBALS['_GET']['REQUEST'])):

		if(is_dir(root(PATH_TEMPLATE.'/'.$GLOBALS['_GET']['REQUEST']))):
			$dirname = dirname($GLOBALS['_GET']['REQUEST']);
			if(substr($GLOBALS['_GET']['REQUEST'], -1) == '/'):
				$filename = str_replace($dirname, '', substr($GLOBALS['_GET']['REQUEST'], 0, -1));
			else:
				$filename = str_replace($dirname, '', $GLOBALS['_GET']['REQUEST']);
			endif;
			tpl_static_page(str_replace('//', '/', $GLOBALS['_GET']['REQUEST'].$filename));
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
