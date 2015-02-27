<?php

    /**
     * This is the file to define routes, filters an patterns.
     * You can choose one of the two methods for routes définition :
     * with the XML manifest file thanks to Manifest::load() method
     * or with custom routes définition (Manifest::register(...)).
     *
     * Note :
     * You can also use these both methods but this is not adviced
     * in order to keep a one place routes definition.
    **/

	Router::register('module', '$action' , array(
		'uri' => '...',
		'parameters' => '...',
		'method' => '...',
		'domain' => '...',
		'filters' => '...',
	));

	Router::register('controller:action', array(
		'parameters' => array(
			'...'
		)
	));

	/**
	 * Register filters
	 *
	**/
	Router::filter('lng_UE', function($route, $parameters) {
		return in_array(Request::language, array(
			'fr_FR', 'fr_BE',
		));
	});


	Router::filter('name', function($route) {
		// Execute filter
		// return bool true/false
	});

	/* Example of patterns
        Router::pattern('id', '[\d]+');
        Router::pattern('locale', '[a-z]{2,3}_[A-Z]{2,3}');
    */


	/**
	 * Register routes and patterns from manifest files
	 * This will use or cached manifest either original one
	**/
	$manifest = SYSTEM_ROOT.PATH_VAR.'/manifest.xml';
	$tmp = SYSTEM_ROOT.PATH_TMP.'/manifest.json';

	if(!SYSTEM_DEBUG && file_exists($tmp) && filemtime($manifest) < filemtime($tmp)) {

		// Read cached file
		$data = json_decode(file_get_contents($tmp), true);

		if(!empty($data['routes'])) { // Register routes
			foreach($data['routes'] as $route) {
				$router->register($route['method'], $pattern['value']);
			}
		}

		if(!empty($data['patterns'])) // Register pattenrs
			foreach($data['patterns'] as $pattern)
				$router->pattern($pattern['name'], $pattern['value']);

	}
	else {

		$dom = new DOMDocument();
		$dom->load($manifest);
		$manifest = $dom->getElementsByTagName('manifest')->item(0);

		// Parse global patterns
		foreach($manifest->getElementsByTagName('pattern') as $pattern) {
			Router::register($pattern->getAttribute('name'), $pattern->getattribute('regexp'));
		}

		// Parse standalone requests
		foreach($manifest->getElementsByTagName('route') as $route) {

			if($route->hasAttribute('action'))
				trigger_error('Manifest: undefined attribute action', E_USER_ERROR);

			$parameters = array();
			foreach($route->getElementsByTagName('param') as $param) {
				$parameters[$param->getAttribute('name')] = $param->getAttribute('regexp');
			}

			Router::register($route->getAttribute('action'), array(
				'uri' => ($route->hasAttribute('uri') ? $route->getAttribute('uri') : ''),
				'method' => ($route->hasAttribute('method') ? explode('|', $route->getAttribute('method')) : array()),
				'languages' => ($route->hasAttribute('languages') ? explode('|', $route->getAttribute('languages')) : array()),
				'parameters' => $parameters,
				'domain' => ($route->hasAttribute('domain') ? str_replace('~', SYSTEM_DOMAIN, $route->getAttribute('domain')) : null),
				'filter' => ($route->hasAttribute('filter') ? $route->getAttribute('filter') : null),
			));

		}

		if(!SYSTEM_DEBUG) { // Register cached manifest
			mkpath(root(PATH_TMP));
			$json = fopen($tmp, 'w+');
			fwrite($json, json_encode(array(
				'routes' => self::$router,
				'patterns' => self::$patterns
			)));
			fclose($json);
		}
	}

	/*
	$router->filter('cli', function() {
		echo 'filter cli called';
		return true;
	});
	*/

	return $router;
