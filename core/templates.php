<?php

	function tpl_static_page($page) {
		if(file_exists(root(PATH_TEMPLATE."/$page.php"))):
			include(root(PATH_TEMPLATE."/$page.php"));
		else:
			include(root(PATH_TEMPLATE."/404.php"));
		endif;
	}
	
	function tpl_headers($dir = '/inc') {
		include(root(PATH_TEMPLATE."/$dir/headers.php"));
	}
	
	function tpl_banner($dir = '/inc') {
		include(root(PATH_TEMPLATE."/$dir/banner.php"));
	}
	
	function tpl_footer($dir = '/inc') {
		include(root(PATH_TEMPLATE."$dir/footer.php"));
	}
	
	function tpl_sidebar($dir = '/inc') {
		include(root(PATH_TEMPLATE."$dir/sidebar.php"));
	}
	
	function get_page() {
		if(!empty($GLOBALS['_GET']['page'])):
			return intval($GLOBALS['_GET']['page']);
		else:
			return 1;
		endif;	
	}
		
	function get_parameter($name) {
		if(!empty($GLOBALS['_GET']['PARAMETERS'][$name])):
			return $GLOBALS['_GET']['PARAMETERS'][$name];
		else:
			return false;
		endif;
	}
	
?>