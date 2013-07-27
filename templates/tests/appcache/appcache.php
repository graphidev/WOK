<!doctype html>
<html manifest="<?php echo path('/appcache/manifest'); ?>">


	<head>
		<title>AppCache</title>
		<script src="<?php echo path('appcache/javascript'); ?>"></script>
	</head>

	<body>

		<script>
			alert('document loaded');
			window.onload = function() {
				
				appcache = window.applicationCache;
				if(!appcache) { alert('appcache works'); }

			}
			
		</script>

	</body>

</html>