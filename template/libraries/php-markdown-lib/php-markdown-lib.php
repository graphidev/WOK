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
            
            <ul class="breadcrumb">
                <li><a href="<?php echo path(); ?>">WOK</a> <span class="divider">/</span></li>
                <li><a href="<?php echo path('libraries'); ?>">External libraries</a> <span class="divider">/</span></li>
                <li>PHP Markdown Lib</li>
            </ul>
            
            
            <div class="content">
                
                <div class="rows">
                    
                    <div class="span3">
                    
                    
                    </div>
                
                    <div class="span7">
                    
                        
                        <form action="" method="post">
                            <textarea name="markdown" class="input-block-level" rows="20" placeholder="Type some markdown ..."><?php echo file_get_contents(root('/files/samples/markdown.md')); ?></textarea>
                            <input type="submit" value="Preview" class="btn btn-primary btn-block" />
                        </form>
                        
                        <div id="preview" class="well"></div>
                    </div>
                </div>
            </div>
            
        </div>
    
    </body>
    

</html>