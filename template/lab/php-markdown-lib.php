<?php

    if(!empty($_POST['markdown'])):
        
        get_library('php-markdown-lib');
        exit(markdown($_POST['markdown']));

    endif;

?>
<html>
    
    <head>
        <title>Markdown | Lab | WOK</title>
        
        <?php tpl_headers(); ?>
        
        <script>
        
            $(document).ready(function() {
                $('form').on('submit', function(event) {
                    
                    event.preventDefault();
                    event.stopPropagation();
                    
                     $.post('<?php echo path(get_request()); ?>', { markdown:$('textarea[name="markdown"]').val() }, function(data) {
                        $('#preview').html(data); 
                     });
                    
                    return false;
                    
                });
            });
        
        </script>
        
    </head>
    
    <body>
        
        <div id="main">
            
            <?php tpl_banner(); ?>
            
            <div class="content">
                
                
                <form action="" method="post">
                    <textarea name="markdown" class="input-block-level" rows="20" placeholder="Type some markdown ...">Titre de niveau 1
=================

Titre de niveau 2
-----------------
Voici un mot *important* à mon sens
Voici un mot _important_ à mon sens
                        
# Titre de niveau 1

## Titre de niveau 2

### Titre de niveau 3
                        
* Une puce
* Une autre puce
* Et encore une autre puce !
                    </textarea>
                    <input type="submit" value="Preview" class="btn btn-primary btn-block" />
                </form>
                
                <div id="preview"></div>
            </div>
            
        </div>
    
    </body>
    

</html>