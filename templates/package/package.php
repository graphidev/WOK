<html>
    
    <head>
        <title><?php _t('package:pagename'); ?></title>
        
        <?php Response::inc('inc/headers', PATH_TEMPLATES); ?>
        
    </head>
    
    <body>
        
        <div id="main" >
                        
            <div class="content">
                
                <div class="hero-unit">
                        
                    <?php 
                        $data = json_decode(file_get_contents(root(PATH_FILES.'/data/'.Session::$language.'/package.psdf')), true); 
                        echo PSDF::parse($data); 
                    ?>
                    <!--
                    <h1><?php _t('package:title'); ?></h1>          
                    
                    
                    <div class="content">
                        
                        <p><?php echo nl2br(_e('package:content.hang')); ?></p>
                        
                        <ul>
                            <li><?php _t('package:content.tools.mvc'); ?></li>
                            <li><?php _t('package:content.tools.multilingual'); ?></li>
                            <li><?php _t('package:content.tools.libraries'); ?></li>
                            <li><?php _t('package:content.tools.open'); ?></li>
                            <li><?php _t('package:content.tools.more'); ?></li>
                        </ul>
                        
                        <p class="text-right"><?php _t('package:content.doubt'); ?></p>
                        
                    </div>
                    -->
                    
                    <div class="buttons">
                        <a href="<?php echo path('/'); ?>" class="btn btn-inverse btn-large pull-left">« <?php _t('buttons.previous'); ?></a>
                        <a href="<?php echo path('/package/controller'); ?>" class="btn btn-primary btn-large pull-right"><?php _t('buttons.next'); ?> »</a>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                </div>
                
            </div>
           
            
        </div>
    
    </body>
    

</html>