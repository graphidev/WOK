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
        
	require_once SYSTEM_ROOT.PATH_CORE . '/templates.php';
    

    /**
     * Set Custom controllers
    **/
    if(file_exists(root('/manifest.php')))
        require_once(root('/manifest.php'));
    

    /**
     * Set API's controller
    **/
    Controller::add(function($query) {
            return preg_match('#^api/(.+)?$#', $query);
        }, function($query) {
            $api = root(PATH_APIS.'/'.preg_replace('#^api/(.+)?$#', '$1', $GLOBALS['_GET']['REQUEST']).'.php');
            if(file_exists($api)):
                include_once($api);
            else:
                header('Content-type: application/json');
                echo json_encode(array('error'=>true, 'info'=>'API doesn\'t exists'));
            endif;
        }, true);
    

    /**
     * Set static pages controllers
    **/    
    Controller::add(function($query) {
            return empty($query);
        }, 
        function() {
            tpl_static_page('homepage');
        }, true);
    

    // This is the default controller
    Controller::add(true, function($query) {
        /**
         * Directory, check the file with the same name in this directory
        **/
        if(is_dir(root(PATH_TEMPLATES.'/'.substr($query, 0, -1)))):         
            $dirname = dirname($query);
    
            if(substr($GLOBALS['_GET']['REQUEST'], -1) == '/'):
                 $filename = str_replace($dirname, '', substr($query, 0, -1));
            else:
                $filename = str_replace($dirname, '', $query);
            endif;
            
            tpl_static_page(str_replace('//', '/', $query.$filename));
            
        /**
         * File, check the filename
        **/
        else:   
            tpl_static_page($query);
    
        endif;
    }, true);
    

    /**
     * Invoke the controller queue
    **/
    Controller::invoke($GLOBALS['_GET']['REQUEST']);

?>
