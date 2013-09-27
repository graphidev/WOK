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
                    
                    <div class="buttons text-center">
                        <a href="<?php echo path('/structure'); ?>" class="btn btn-success btn-large btn-block"><?php _t('buttons.start'); ?></a>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                </div>
                
            </div>
           
            
        </div>
    
    </body>
    

</html>