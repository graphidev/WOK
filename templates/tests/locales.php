<html>
    
    <head>
        <title>Locales | Web Operational Kit</title>
        
        <?php tpl_headers(); ?>
        
        <style>
            pre code {
                outline:0;
                display: block;
            }
        </style>
        
    </head>
    
    <body>
        
        <div id="main">
        
            <?php 
                
                echo _e('default:menu.home');
                echo ' | ';
                $data = array(
                    'year'=> date('Y'), 
                    'owner'=> 'Sébastien ALEXANDRE'
                );
                echo _e('default:footer.credits', $data);
            ?>
            
            <hr />
            
            
            
            <?php
                $array = array(
                    'navigation' => array(
                        'home' => 'Accueil',
                        'about' => 'À propos',
                    )
                );

                //echo json_encode($array);
            ?>
            
        </div>
    
    </body>
    

</html>