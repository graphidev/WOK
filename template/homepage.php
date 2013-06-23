<html>
    
    <head>
        <title>Web Operational Kit</title>
        
        <?php tpl_headers(); ?>
        
    </head>
    
    <body>
        
        <div id="main">
            
            <?php tpl_banner(); ?>
            
            <h1>Welcome on homepage</h1>
            
            <pre><?php print_r(tree(root('/template'))); ?></pre>
           
            
        </div>
    
    </body>
    

</html>