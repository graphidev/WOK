<?php

	/**
     * Initialize WOK
    **/
	require_once "core/init.php";

?>

<html>

    <head>
        
        <meta charset="UTF-8">
        
        <title>WOK setup</title>
        
        <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="libs/bootstrap/css/bootstrap-responsive.min.css" type="text/css">
        
        <script src="libs/bootstrap/js/bootstrap.js"></script>
        <script src="libs/jquery/jquery-1.10.1.min.js"></script>
        
        <script>
            $(document).ready(function() {
                
                $('#protocol, #domain, #directory').on('input load', function() {
                    var protocol = $('#protocol').val();
                    var domain = $('#domain').val();
                    var directory = $('#directory').val();
                    $('#address').val(protocol + domain + directory);
                });
                
                $('.btn.setup_mode').click(function() {
                    var mode = $(this).attr('data-setup');
                    $('.mode#'+mode).show();
                    $('.mode:not(#'+mode+')').hide();
                });
                
            });
        </script>
        
        <style>
        
            body {
                background: #eee;
            }
            
            #main {
                background: #fff;
                padding: 40px;
                width: 744px;
                margin: 40px auto;
                -moz-border-radius: 8px;
                -webkit-border-radius: 8px;
                border-radius: 8px;
                border: 1px solid #ccc;
            }
            
            .mode#by_hand {
                display: none;   
            }
            
            h2 {
                margin: 40px 0;   
            }
            
            p {
                margin: 15px 0;   
            }
            
            form {
                display: block;
                
            }
            
            form label {
                font-weight: bold;   
            }
            
        </style>
        
    </head>
    
    <body>
    
        <div id="main">
            
            <?php

                if(!empty($_POST['setup'])):
                    
                    $settings = file_get_contents('core/settings-default.php');
                    $settings = preg_replace("#define\('SYSTEM_DEFAULT_PROTOCOL', '(.+)?'\)#", "define('SYSTEM_DEFAULT_PROTOCOL', '".$_POST['protocol']."')", $settings);
                    $settings = preg_replace("#define\('SERVER_DOMAIN', '(.+)?'\)#", "define('SERVER_DOMAIN', '".$_POST['domain']."')", $settings);
                    $settings = preg_replace("#define\('SYSTEM_DIRECTORY_PATH', '(.+)?'\)#", "define('SYSTEM_DIRECTORY_PATH', '".$_POST['directory']."')", $settings);
                    $settings = preg_replace("#define\('SYSTEM_TIMEZONE', '(.+)?'\)#", "define('SYSTEM_TIMEZONE', '".$_POST['timezone']."')", $settings);
                    $settings = preg_replace("#define\('SESSION_CRYPT', '(.+)?'\)#", "define('SESSION_CRYPT', '".sha1(uniqid('sess_'))."')", $settings);
                    $settings = preg_replace("#define\('TOKEN_SALT', '(.+)?'\)#", "define('TOKEN_SALT', '".sha1(uniqid('tok_'))."')", $settings);
                    $settings = preg_replace("#define\('COOKIE_CRYPT', '(.+)?'\)#", "define('COOKIE_CRYPT', '".sha1(uniqid('cook_'))."')", $settings);
                    file_put_contents('core/settings.php', $settings);


                    $htaccess = file_get_contents('.htaccess.default');
                    $htaccess = str_replace('__WOK_DIR__', $_POST['directory'], $htaccess);
                    file_put_contents('.htaccess', $htaccess);

                    //unlink('setup.php');
                    
            ?>
            
                <h2 class="text-center">Setup completed</h2>
            
                <p class="text-center">
                    WOK Framework is now working fine. <br />
                    Here begin your job. Enjoy !
                </p>
            
                <a href="<?php echo $_POST['protocol'].$_POST['domain'].$_POST['directory']; ?>" class="btn btn-success btn-block btn-large">Start using WOK !</a>
            
            <?php else: ?>
            
                <h2 class="text-center">Welcome in WOK Setup</h2>
                
                <div class="mode" id="by_hand">
            
                    <div class="alert">
                        <button type="button" class="btn btn-mini btn-primary pull-right setup_mode" data-setup="by_form">Setup by form</button>
                        We inform you that the form could be a better solution to configure WOK Framework.
                    </div>
                    
                    <p>
                        There are the configuration to do step by step :
                    </p>
                    
                    <ul>
                        <li>Duplicate /core/settings-default.php and rename it settings.php</li>
                        <li>Define configuration constants (See comments' help)</li>
                        <li>Create a .htaccess file on WOK root containing ...</li>
                    </ul>
                    
                    <pre><code># Always redirect to index.php
# this file analyse and treats all HTTP requests
&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine On
    RewriteBase __WOK_DIR__
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    RewriteRule . __WOK_DIR__/index.php [L]
&lt;/IfModule&gt;</code></pre>
                    
                    <ul>
                        <li>Replace __WOK_DIR__ by the WOK directory (eg. /wok)</li>
                        <li>Remove setup.php file (could be a security flaw)</li>
                    </ul>
                    
                </div>
                
                <form action="setup.php" method="post" class="mode" id="by_form">
                        
                    <div class="alert alert-info">
                        <button type="button" class="btn btn-mini btn-info pull-right setup_mode" data-setup="by_hand">Setup by hand</button>
                        The following form will help you to configure WOK framework.
                    </div>
                    
                    <h3>Required informations</h3>
                    
                    <label for="protocol">Default protocol</label>
                    <input class="input-block-level" type="text" name="protocol" id="protocol" value="http://" placeholder="http://" required>
                    
                    
                    <label for="domain">Server domain</label>
                    <input class="input-block-level" type="text" name="domain" id="domain" value="<?php echo $_SERVER['SERVER_NAME']; ?>" placeholder="http://localhost/wok" required>
                    

                    <label for="directory">WOK directory</label>
                    <input class="input-block-level" type="text" name="directory" id="directory" value="<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', SYSTEM_ROOT); ?>" placeholder="/wok">
                    
                    
                    <label for="address">System address</label>
                    <input class="input-block-level" type="url" name="address" id="address" value="http://<?php echo $_SERVER['SERVER_NAME'].str_replace($_SERVER['DOCUMENT_ROOT'], '', SYSTEM_ROOT); ?>" placeholder="http://localhost/wok" required disabled>
                    
                    
                    
                    <label for="timezone">System timezone</label>
                    <select name="timezone" class="pull-right input-block-level" required>
                        <option value="">System timezone</option>
                        <?php 
                            include_once('core/timezones.php'); 
                            
                            foreach(get_timezones() as $area => $cities) {
                                
                                echo '<optgroup label="'.$area.'">';
                                
                                foreach($cities as $i => $name) {
                                    
                                    if($area.'/'.$name == date_default_timezone_get()):
                                        echo '<option value="'.$area.'/'.$name.'" selected="selected">'.$area.'/'.$name.'</option>';
                                    else:
                                        echo '<option value="'.$area.'/'.$name.'">'.$area.'/'.$name.'</option>';
                                    endif;
                                }
                                
                                echo '</optgroup>';
                                
                            }
                        ?>
                    </select>
                    
                    <?php
                        
                        if(@is_writable(SYSTEM_ROOT) && @is_writable(SYSTEM_ROOT.'/core/')):
                    ?>
                    
                        <input type="submit" class="btn btn-success btn-block btn-large" name="setup" value="Setup" />
                    
                    <?php else: ?>
                        
                        <div class="alert alert-error">
                    
                            Write permissions are not available : setup.php need them to generate configuration files. 
                            
                        </div>
                    
                    <?php endif; ?>
                </form>
            
            <?php endif; ?>
            
        </div>
        
    </body>
    
</html>
