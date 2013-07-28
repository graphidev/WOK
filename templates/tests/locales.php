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
                global $session; 

                if(!empty($_POST['token'])):
                    if($session->is_authorized_token($_POST['token'], 1)):
                        echo '<div class="alert alert-info">'._e('token.authorized').'</div>';
                    else:
                        echo '<div class="alert alert-error">'._e('token.unauthorized').'</div>';
                    endif;
                else:
            ?>
                <form action="#" method="POST">
                    <input type="hidden" name="token" value="<?php echo $session->token(); ?>" />
                    <input type="submit" class="btn btn-primary" value="check token" />
                </form>
            <?php
                endif;
            ?>
            
            
            
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