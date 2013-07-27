<?php
	header("Content-type: text/javascript");
	echo file_get_contents(root('/template/appcache/script.js'));
?>