<html>
    
    <head>
        <title><?php _t('about:pagename'); ?></title>
        
        <?php tpl_headers(); ?>
        
    </head>
    
    <body>
        
        <div id="main" >
                        
            <div class="content">
                
                <div class="hero-unit">
                    
                    <h1><?php _t('about:title'); ?></h1>          
                    
                    
                    <div class="content">
                        
                        <p><?php echo nl2br(_e('about:content.hang')); ?></p>
                        
                        <ul>
                            <li><?php _t('about:content.list.compatibility'); ?></li>
                            <li><?php _t('about:content.list.mvc'); ?></li>
                            <li><?php _t('about:content.list.multilingual'); ?></li>
                            <li><?php _t('about:content.list.libraries'); ?></li>
                            <li><?php _t('about:content.list.open'); ?></li>
                            <li><?php _t('about:content.list.more'); ?></li>
                        </ul>
                        
                        <p class="text-right"><?php _t('about:content.doubt'); ?></p>
                        
                    </div>
                    
                    <div class="buttons">
                        <a href="<?php echo path('/'); ?>" class="btn btn-inverse btn-large pull-left">« <?php _t('buttons.previous'); ?></a>
                        <a href="<?php echo path('/functions'); ?>" class="btn btn-primary btn-large pull-right"><?php _t('buttons.next'); ?> »</a>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                </div>
                
            </div>
           
            
        </div>
    
    </body>
    

</html>