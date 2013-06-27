<html>
    
    <head>
        <title>Start | Web Operational Kit</title>
        
        <?php tpl_headers(); ?>
        
    </head>
    
    <body>
        
        <div id="main">
            
            <?php tpl_banner(); ?>
            
            <ul class="breadcrumb">
                <li><a href="<?php echo path(); ?>">WOK</a> <span class="divider">/</span></li>
                <li>Basic usage of the framework</li>
            </ul>
            
            <div class="content">
                
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
                    Then you have to follow the follow the guidelines in setup.php (you got the choice to make your custom configuration or validate proposed parameters). That's it !
                </p>
                
                <h2>Framework organisation</h2>
                
                <p>
                    Based on an MVC and an open structures, WOK have three main folders : 
                </p>
                
                <ul>
                    <li><b>/core</b> - Required functions, default tools and configuration file</li>
                    <li><b>/libs</b> - Additional libraries, social networks SDKs and other frameworks (Bootstrap, jQuery, ...)</li>
                    <li><b>/template</b> - Templates files called by the index.php file</li>
                </ul>
                
            </div>
            
        </div>
    
    </body>
    

</html>