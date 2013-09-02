<html>
    
    <head>
        <title>Page de tests</title>
        
        <?php Response::inc('inc/headers') ?>
        
        <style>
            q:before {
                content:"« ";
            }
            q:after {
                content: " »";
            }
        </style>
        
    </head>
    
    <body>
        
        <div id="main" class="error404" >
                        
            <div class="content">
               <?php
                    //include(root('/templates/editor-data.php'));
                    _t('discover:pagename')
                ?>
            </div>
           
            
        </div>
    
    </body>
    

</html>