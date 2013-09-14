<html>
    
    <head>
        <title><?php echo Response::$data->title ?></title>
        
        <?php Response::inc('inc/headers', PATH_TEMPLATES); ?>
        
    </head>
    
    <body>
        
        <div id="main" >
            
            <?php Response::inc('inc/navbar', PATH_TEMPLATES); ?>
                        
            <div class="content">
                
                <div class="jumbotron">

                    <?php echo Response::$data->content ?>
                    
                    <div class="buttons">
                        <a href="<?php echo path('/package/'); ?>" class="btn btn-primary btn-large"><?php _t('buttons.more'); ?> »</a>
                    </div>
                    
                    <p>
                        <small>
                            <a href="<?php echo path('/finish'); ?>"><?php _t('buttons.donotcare'); ?></a>
                        </small>
                    </p>
                                        
                    <div class="clearfix"></div>
                    
                </div>
                
            </div>
           
            
        </div>
    
    </body>
    

</html>