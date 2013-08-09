<html>
    
    <head>
        <title><?php _t('discover:pagename'); ?></title>
        
        <?php tpl_headers(); ?>
        
    </head>
    
    <body>
        
        <div id="main" >
                        
            <div class="content">
                
                <div class="hero-unit">
                    <div class="thumbnail pull-right">
                        <img src="<?php echo path(PATH_FILES.'/images/300x400.jpeg'); ?>" alt="Placekitten" />
                    </div>
                    
                    <h1><?php _t('discover:title'); ?></h1>
                    <div class="content">
                        
                        <p><?php _t('discover:marketing.hang'); ?></p>
                        <p><?php _t('discover:marketing.desire'); ?></p>
                        
                    </div>
                    
                    <div class="buttons">
                        <a href="<?php echo path('/about'); ?>" class="btn btn-primary btn-large"><?php _t('buttons.more'); ?> »</a>
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