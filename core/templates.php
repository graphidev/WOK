<?php

	function tpl_static_page($name) {
		if(file_exists(root(PATH_TEMPLATE."/$name.php"))):
			include(root(PATH_TEMPLATE."/$name.php"));
		elseif(file_exists(root(PATH_TEMPLATE."/404.php"))):
			include(root(PATH_TEMPLATE."/404.php"));
        else:
            exit("404 Document not found");
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
    
    function get_request() {
        if(!empty($GLOBALS['_GET']['REQUEST'])):
            return $GLOBALS['_GET']['REQUEST'];
        else:
            return false;
        endif;
    }
		
	function get_parameter($name) {
		if(!empty($GLOBALS['_GET']['PARAMETERS'][$name])):
			return $GLOBALS['_GET']['PARAMETERS'][$name];
		else:
			return false;
		endif;
	}

    function get_library($name) {
        
        if(is_dir(root(PATH_LIBS."/$name")) && file_exists(root(PATH_LIBS."/$name/$name.xml"))):
        
            $xml = new DOMDocument();
			$xml->load(root(PATH_LIBS."/$name/$name.xml"));
            $library = $xml->getElementsByTagName('library')->item(0);
            $files = $library->getElementsByTagName('files')->item(0)->getElementsByTagName('file');
            
        
            foreach($files as $i => $case) {
                
                $type = $case->getAttribute('type');
                $path = $case->nodeValue;
                
                if(file_exists(root(PATH_LIBS."/$name$path"))):
                    
                    switch($type) {
                        case 'css':
                            echo '<link href="'.path(PATH_LIBS."/$name$path").'" rel="stylesheet" type="text/css">';
                        break;
                        case 'js':
                            echo '<script type="text/javascript" src="'.path(PATH_LIBS."/$name$path").'"></script>';
                        break;
                        case 'php':
                            include(root(PATH_LIBS."/$name$path"));
                        break;
                        
                    }
                
                endif;
            }
                
        else:
            return false;
        endif;
        
    }
	
?>