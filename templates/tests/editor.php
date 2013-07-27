<html>
    
    <head>
        <title>Template editor | Web Operational Kit</title>
        
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
            
            <?php if(get_parameter('file')): ?>
            
                <h1><?php echo get_parameter('file'); ?></h1>
            
                <?php $format = str_replace('.', '' ,strrchr( get_parameter('file'), '.' )); ?>
                
                <?php if($format == 'css' || $format == 'php' || $format == 'md'): ?>
                    <pre><code class="language-<?php echo $format; ?>" contenteditable="true"><?php echo file_get_contents(root('/template/'.get_parameter('file'))); ?></code></pre>
                
                <?php elseif($format == 'png' || $format == 'jpg' || $format == 'jpeg' || $format == 'gif'): ?>
                
                    
                    
                <?php endif; ?>
            
            <?php else: ?>
                
                <?php 
                    function file_list($array, $folder = '') {
                        foreach($array as $name => $item) {
                            if(is_array($item)):
                                echo "<li>$name<ul>";
                                file_list($item, "$folder/$name");
                                echo'</ul></li>';
                            else:
                                echo '<li><a href="?file='.urlencode($folder.'/'.$name).'">'.$name.'</a></li>';
                            endif;
                        }
                    }
                ?>
                
                <ul>
                    <?php file_list(tree(root('/template'))); ?>
                </ul>
            
            <?php endif; ?>
            
        </div>
    
    </body>
    

</html>