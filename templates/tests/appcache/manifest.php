<?php
	header("Content-type: text/cache-manifest");
	echo file_get_contents(root('/template/appcache/appcache-manifest.mf'));
?>