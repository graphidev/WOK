<html>
    
    <head>
        <title>Start | Web Operational Kit</title>
        
        <?php tpl_headers(); ?>
        
    </head>
    
    <body>
        
        <div id="main">
            
            <?php tpl_banner(); ?>
            
            <h1>Basic usage</h1>
            
            <p>We'll see how easy you can use WOK for any of you projects.</p>
            
            <h2>All you need is ...</h2>
            
            <ul>
                <li>An APACHE web server</li>
                <li>PHP 5.1 or higher</li>
                <li>URL Rewriting module available</li>
            </ul>
            
            
            <h2>Let's start !</h2>
            
            <p>
                First of all, you need to copy the WOK folder on your server.
            </p>
            
            <p>
                Then we'll have to configure two files : .htaccess and /core/settings.php
            </p>
            
            <p>
                Because of WOK is working on a MVC model and URL Rewriting module, we need to update the .htaccess file :
            </p>
            
            <pre><code class="language-http"><?php echo file_get_contents(root('/.htaccess')); ?></code></pre>
            
            <p>
                You have to edit these two lines the set the correct controler : replace /wok by the correct server path.
            </p>
            
            <p>
                Now you 
            </p>
            
            <pre><code class="language-php"><?php echo htmlentities(file_get_contents(root('/core/settings.php'))); ?></code></pre>
            
            <p>
                That's it ! You can now use all the WOK tools !
            </p>
            
        </div>
    
    </body>
    

</html>