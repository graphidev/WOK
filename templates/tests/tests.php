<html>
    
    <head>
        <title>Test part | Web Operational Kit</title>
        
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
                    function file_list($array, $folder = '') {
                        foreach($array as $name => $item) {
                            if(is_array($item)):
                                echo '<li><a href="'.path("/tests$folder/$name").'">'.$name.'<ul>';
                                file_list($item, "$folder/$name");
                                echo'</ul></li>';
                            elseif(substr($name, -3) == 'php'):
                                echo '<li><a href="'.path("/tests$folder/".substr($name, 0, -4)).'">'.substr($name, 0, -4).'</a></li>';
                            endif;
                        }
                    }
                ?>
                
                <ul>
                    <?php file_list(tree(root('/template/tests'))); ?>
                </ul>
                        
        </div>
    
    </body>
    

</html>