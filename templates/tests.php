<html>
    
    <head>
        <title>Page de tests</title>
        
        <?php tpl_headers(); ?>
        
    </head>
    
    <body>
        
        <div id="main" class="error404" >
                        
            <div class="content text-center">
                <?php
                    $data = "Lorem \r\n Ipsum";
                    $lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sit amet purus et neque tincidunt auctor. Donec porttitor enim in faucibus molestie. Aliquam et nulla nulla. Quisque lectus erat, gravida sit amet ultrices vitae, tristique nec mauris. Pellentesque venenatis, mauris a consectetur egestas, nunc purus condimentum dui, sed eleifend erat ipsum iaculis nisi. Fusce ut convallis mauris. Quisque a consectetur metus. Phasellus leo nisi, gravida non magna vel, accumsan lobortis erat. Mauris non lobortis nisl. Praesent malesuada posuere hendrerit. Nunc enim lectus, fringilla non nisi vel, tempus aliquet eros. Fusce fringilla, quam vel laoreet elementum, nunc metus cursus ante, vel ultrices dui orci eu sem. Pellentesque nec lectus sagittis, molestie purus nec, viverra nisi. Ut non odio a turpis mattis aliquet. Aliquam eget posuere eros.";
                ?>
                This is a default data : <?php _t('tests:default', array('input'=>$data)); ?><br /><br />
                
                This is a breaklines data : <?php _t('tests:formats.breaklines', array('data'=>$data)); ?>
                <br /><br />
                
                This is a date format : <?php _t('tests:formats.datetime', array('date' => date('Y-m-d H:i:s'))); ?><br /><br />
                
                This is a money format : <?php _t('tests:formats.money', array('money' => 789.5345)); ?><br /><br />
                
                This is a variable format : <?php _t('tests:formats.variable'); ?><br /><br />
                
                This is a resumed format :  <?php _t('tests:formats.resume', array('input' => $lorem)); ?><br /><br />
                
                This is a reverse format :  <?php _t('tests:formats.reverse', array('input' => resume($lorem,5))); ?><br /><br />
                
                This is a upper/lower format :  <?php _t('tests:formats.upper', array('input' => resume($lorem,2))); ?> / <?php _t('tests:formats.lower', array('input' => resume($lorem,2))); ?>
            </div>
           
            
        </div>
    
    </body>
    

</html>