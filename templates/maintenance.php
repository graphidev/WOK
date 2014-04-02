<!DOCTYPE html>
<html>
	<head>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <meta http-equiv="refresh" content="5; URL=<?php echo path(\Request::uri()); ?>">
        <link rel="stylesheet" href="<?php echo path(PATH_TEMPLATES.'/css/default.css'); ?>">
        <title><?php echo _e('statics:maintenance.title'); ?></title>
        
	</head>

	<body>
        
        <div id="main">
            <h1 id="js"><?php echo _e('statics:maintenance.legend'); ?></h1>
            <p><img src="<?php echo path(PATH_TEMPLATES.'/img/updating.gif'); ?>" /></p>
        </div>
        
	</body>
</html>