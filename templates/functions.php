<html>
    
    <head>
        <title><?php _t('functions:pagename'); ?></title>
        
        <?php tpl_headers(); ?>
        
    </head>
    
    <body>
        
        <div id="main" >
                        
            <div class="content">
                
                <div class="hero-unit">
                    
                    <h1><?php _t('functions:title'); ?></h1>          
                    
                    
                    <div class="content">
                        <p><?php _t('functions:about'); ?></p>
                        
                        <ul>
                            <li>
                                Templates functions
                                <ul>
                                    <li>get_static_page</li>
                                    <li>get_headers : </li>
                                    <li>get_banner</li>
                                    <li>get_sidebar</li>
                                    <li>get_footer</li>
                                </ul>
                            </li>
                        </ul>
                        
                        <p class="text-right">
                            <small>
                                <a href="https://github.com/graphidev/WOK/wiki" target="_blank">For more informations, please visit the WOK WIKI</a>
                            </small>
                        </p>
                    </div>
                    
                    <div class="buttons">
                        <a href="<?php echo path('/about'); ?>" class="btn btn-inverse btn-large pull-left">« <?php _t('buttons.previous'); ?></a>
                        <a href="<?php echo path('/locales'); ?>" class="btn btn-primary btn-large pull-right"><?php _t('buttons.next'); ?> »</a>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                </div>
                
            </div>
           
            
        </div>
    
    </body>
    

</html>