<html>

    <head>
        
        <title>This is a first page</title>
        
        <?php tpl_headers(); ?>
        <!--
        <meta http-equiv="refresh" content="1; URL=<?php echo path('?no_homescreen=1'); ?>">
        -->
        <style>
            #main {
                position:absolute;
                top: 50%; left: 50%;
                width:600px;
                height: 400px;
                margin-left:-300px;
                margin-top:-200px;
            }
            
            h1.bigger {
                font-size:5em;
                margin: 80px 0 100px 0;
            }
        </style>
        
        <script>
        
            $(document).ready(function() {
                
                var timer = 10;
                
                $('.btn.btn-success').html('<span id="seconds">'+timer+'</span> seconds left');
                
                setInterval(function() {
                    timer -= 1;
                    
                    if(timer <= 0) {
                        
                        window.location = $('.btn.btn-success').attr('href');
                        
                        
                    } else {
                     
                        $('#seconds').text(timer);
                        
                    }
                    
                }, 1000);
                
                $('.btn.btn-success').on('mouseover', function() {
                    $(this).html("I can't wait to see it !"); 
                    
                    $('.btn.btn-success').on('mouseout', function() {
                        $(this).html('<span id="seconds">'+timer+'</span> seconds left'); 
                    });
                    
                });
                
            });
            
        </script>
        
    </head>
    
    <body>
        
        <div id="main" class="well text-center">
       
            <h1>Are you a PHP developer ?</h1>
            <h2>Are you tired of complicated frameworks ?</h2>
            <h1 class="bigger">This is for you !</h1>
            <a class="btn btn-success btn-block" href="<?php echo path('homepage'); ?>"></a>
            
        </div>
        
    </body>
    
</html>