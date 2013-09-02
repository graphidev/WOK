<html>
    
    <head>
        <title><?php _t('controller:pagename'); ?></title>
        
        <?php Response::inc('inc/headers', PATH_TEMPLATES); ?>
        
    </head>
    
    <body>
        
        <div id="main" >
                        
            <div class="content">
                
                <div class="hero-unit">
                    
                    <h1><?php _t('controller:title'); ?></h1>          
                    
                    
                    <div class="content">
                        <p><?php _t('controller:about'); ?></p>
                        
                        
                        <pre><code>Controller::add($conditions, $action, $strict = false);</code></pre>
                        
                        <div class="alert">
                            <?php _t('controller::advice'); ?>
                        </div>
                        
                        <ul>
                            <li>Controller : <i><?php _t('controller:tools.controller'); ?></i></li>
                            <li>Locales' : </li>
                            <li>get_banner</li>
                            <li>get_sidebar</li>
                            <li>get_footer</li>
                        </ul>
                        
                        <p class="text-right">
                            <small>
                                <a href="https://github.com/graphidev/WOK/wiki" target="_blank">For more informations, please visit the WOK WIKI</a>
                            </small>
                        </p>
                    </div>
                    
                    <div class="buttons">
                        <a href="<?php echo path('/package/'); ?>" class="btn btn-inverse btn-large pull-left">« <?php _t('buttons.previous'); ?></a>
                        <a href="<?php echo path('/controller'); ?>" class="btn btn-primary btn-large pull-right"><?php _t('buttons.next'); ?> »</a>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                </div>
                
            </div>
           
            
        </div>
    
    </body>
    

</html>