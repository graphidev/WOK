<html>
    
    <head>
        <title><?php echo Response::$data->title; ?></title>
        
        <?php Response::inc('inc/headers', PATH_TEMPLATES); ?>
        
    </head>
    
    <body>
        
        <div id="main" >
                        
            <div class="content">
                
                <div class="jumbotron">
                    
                    <?php echo Response::$data->content; ?>

                    <div class="buttons">
                        <a href="<?php echo path('/package/controller'); ?>" class="btn btn-info btn-large pull-left">« <?php _t('buttons.previous'); ?></a>
                        <a href="<?php echo path('/package/libraries'); ?>" class="btn btn-primary btn-large pull-right"><?php _t('buttons.next'); ?> »</a>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                </div>
                
            </div>
           
            
        </div>
    
    </body>
    

</html>