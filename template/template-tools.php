<html>
    
    <head>
        <title>Template | Web Operational Kit</title>
        
        <?php tpl_headers(); ?>
        
        <script>
            $(document).ready(function() {
                
                var tool = null;
                $('.nav.nav-list li a').on('click', function() {
                    $('.nav.nav-list li').removeClass('active');
                    $(this).parent('li').addClass('active');
                    tool = $(this).attr('href');
                });
                
                $('.nav.nav-tabs li a').on('click', function(event) {
                    event.stopPropagation();
                    //event.prependDefault();
                    
                    var tool = $(this).parent('li').parent('ul').attr('data-tool');
                    var open = $(this).attr('data-open');
                    
                    $('#'+tool+' .nav.nav-tabs li').removeClass('active');
                    $(this).parent('li').addClass('active');
                    
                    $('#'+tool+' .tab').addClass('hidden');
                    $('#'+tool+' .tab.'+open).removeClass('hidden');
                    
                    return false;
                });
                
            });
        </script>
        
        <style>
            .nav.nav-tabs li.default {
                font-weight: bold;   
            }
        </style>
        
    </head>
    
    <body>
        
        <div id="main">
            
            <?php tpl_banner(); ?>
            
            <ul class="breadcrumb">
                <li><a href="<?php echo path(); ?>">WOK</a> <span class="divider">/</span></li>
                <li>Template tools</li>
            </ul>
            
            <div class="row">
            
                <div class="span3">
                    <div class="well" style="padding:0;">
                        <ul class="nav nav-list">
                            <li class="nav-header">Templates</li>
                            <li class="active"><a href="#introduction">Introduction</a></li>
                            <li><a href="#reserved_template_files">Reserved template files</a></li>
                            <li><a href="#new_template_file">Create a new template file</a></li>
                            
                            <li class="nav-header">Templates fonctions</li>
                            <li><a href="#tpl_headers">tpl_headers</a></li>
                            <li><a href="#tpl_banner">tpl_banner</a></li>
                            <li><a href="#tpl_sidebar">tpl_sidebar</a></li>
                            <li><a href="#tpl_footer">tpl_footer</a></li>
                            <li><a href="#get_library">get_library</a></li>
                            
                            <li class="nav-header">HTTP Requests</li>
                            <li><a href="#get_request">get_request</a></li>
                            <li><a href="#get_parameter">get_parameter</a></li>
                            <li><a href="#globals_variables">Globals variables</a></li>
                                                            
                        </ul>
                    </div>
                    
                </div>
                
                <div class="span7">
                    
                    <div id="introdution">
                        <h1>Template</h1>
                        
                        <p>
                            WOK is an MVC based framework. That's why we made a really simple system to create templates. All you have to do is to create files. WOK will call them on the correct HTTP request. Some samples ? *
                        </p>
                        
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>HTTP Request</th>
                                    <th>Template file path</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>/about</td>
                                    <td>/about.php</td>
                                </tr>
                                <tr>
                                    <td>/company</td>
                                    <td>
                                        /company/company.php<br />
                                        /company.php
                                    </td>
                                </tr>
                                <tr>
                                    <td>/company/our-philosophy</td>
                                    <td>/company/our-philosophy.php</td>
                                </tr>
                                <tr>
                                    <td>/...</td>
                                    <td>/....php</td>
                                </tr>
                            </tbody>
                        </table>
                        <p>
                            * Whatever the page name, you can create as many page as you want, this rule will always be applied :<br />
                            <i><b>The template path is exactly the same as the HTTP request. Just add .php</b></i><br />
                            The template files are in <i>/template</i> by default and given by the PATH_TEMPLATE constant.
                        </p>
                        
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="reserved_template_files">
                    
                        <h2>Reserved template files</h2>
                        
                        <p>
                            we can see your eyes saying <quote>"Oh no ! Restricted stuffs again !"</quote><br />
                            Well ... no ! Just two names. We won't tell you how to name your files :
                        </p>
                        <ul>
                            <li><b>homepage.php</b> - <i>Will be called on an empty HTTP request (the website homepage, easy).</i></li>
                            <li><b>404.php</b> - <i>An explanation ? really ? Will be called if the requested page/file is not found</i></li>
                        </ul>
                        
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="new_template_file">
                    
                        <h2>Create a new template file</h2>
                        
                        <p>
                            Framework means help you. That's why you will have access to all the tools on each page you will create. You don't have to call any file, they are still included for you.
                        </p>
                        
                        <p>
                            Just create a file in the template folder. For example the 404.php :
                        </p>
                        <pre><code class="language-html"><?php echo htmlentities(file_get_contents(root('/template/404.php'))); ?></code></pre>
                        <p>
                            We suggest you to create the homepage and the 404 page at least.
                        </p>
                    </div>
                    
                    <hr class="separator">
                    
                    
                    <h2>Templates functions</h2>
                    
                    <p>
                        The following functions will help you to separate your page and the common parts.
                    </p>
                    
                    <hr class="separator" />
                    
                    <div id="tpl_headers">
                        <ul class="nav nav-tabs" data-tool="tpl_headers">
                            <li class="default active"><a href="#" data-open="default">tpl_headers</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                We think that common parts should be in a separate folder. That's why this function will take the headers in the /inc folder by default. But you can change it as parameter
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    tpl_headers(); // will include /inc/headers.php
    tpl_headers('/commons'); // will include /commons/headers.php
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                
                            </p>
                            
                        </div>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="tpl_banner">
                        <ul class="nav nav-tabs" data-tool="tpl_banner">
                            <li class="default active"><a href="#" data-open="default">tpl_banner</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                Same things than before.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    tpl_banner(); // will include /inc/banner.php
    tpl_banner('/commons'); // will include /commons/banner.php
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                
                            </p>
                            
                        </div>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="tpl_sidebar">
                        <ul class="nav nav-tabs" data-tool="tpl_sidebar">
                            <li class="default active"><a href="#" data-open="default">tpl_sidebar</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                Same things than before.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    tpl_sidebar(); // will include /inc/sidebar.php
    tpl_sidebar('/commons'); // will include /commons/sidebar.php
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                
                            </p>
                            
                        </div>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="tpl_footer">
                        <ul class="nav nav-tabs" data-tool="tpl_footer">
                            <li class="default active"><a href="#" data-open="default">tpl_footer</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                Same things than before.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    tpl_footer(); // will include /inc/footer.php
    tpl_footer('/commons'); // will include /commons/footer.php
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                
                            </p>
                            
                        </div>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="get_library">
                        <ul class="nav nav-tabs" data-tool="get_library">
                            <li class="default active"><a href="#" data-open="default">get_library</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                This function is different thant the previous. It job is to get external libraries (by default in <i>/libs</i> folder).
                                The libraries files can be CSS, JS, or PHP files. It it's a JS or CSS file, this will write the correct syntax to call them.
                                If there is PHP files, this will include them.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    get_library('jquery'); // will write the JavaScript insertion for jQuery file
    get_library('bootstrap'); // will write JS and CSS insertion of Bootstrap files
    get_library('php-markdown-lib'); // will include the markdown PHP files.
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function return false is the library is not found.
                            </p>
                            
                        </div>
                    </div>
                    
                    <hr class="separator" />
                    
                    <h2>HTTP Requests</h2>
                    
                    Once you will have create your page, you can access to the HTTP request thanks to functions and Globals variables which are :
                    
                    <hr class="separator">
                    
                    <div id="get_request">
                        <ul class="nav nav-tabs" data-tool="get_request">
                            <li class="default active"><a href="#" data-open="default">get_request</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                This function will give you the current HTTP request.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    echo get_request(); // <?php  echo get_request(); ?>    
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                
                            </p>
                            
                        </div>
                    </div>
                    
                    <hr class="separator">
                    
                    <div id="get_parameter">
                        <ul class="nav nav-tabs" data-tool="get_parameter">
                            <li class="default active"><a href="#" data-open="default">get_parameter</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                This function will give some extra GET request parameter's value.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    // HTTP Request : /blog/article?id=1234
    echo get_parameter('id'); // 1234    
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function will return false if the parameter is not found.
                            </p>
                            
                        </div>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="globals_variables">
                    
                        <h2>Globals variables</h2>
                        
                        <p>
                            The previous functions will help you to access to the HTTP requests or part of these. But you also can access to more width the following globals variables :
                        </p>
                        <ul>
                            <li><b>$GLOBALS['_GET']['REQUEST']</b> - <i>Will return the same thing than get_request() function.</i></li>
                            <li><b>$GLOBALS['_GET']['PARAMETERS']</b> - <i>Will return an array containing the $_GET parameters.</i></li>
                            <li><b>$GLOBALS['_POST']</b> - <i>Will return an array containing the $_POST parameters.</i></li>
                        </ul>
                        
                    </div>
                    
                </div>
            
            </div>
           
            
        </div>
    
    </body>
    

</html>