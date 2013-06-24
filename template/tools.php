<html>
    
    <head>
        <title>Tools | Web Operational Kit</title>
        
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
            
            <div class="row">
            
                <div class="span3">
                    <div class="well" style="padding:0;">
                        <ul class="nav nav-list">
                            <li class="nav-header">Tools</li>
                            <li class="active"><a href="#introduction">Introduction</a></li>
                            
                            <li class="nav-header">Utilities fonctions</li>
                            <li><a href="#path">path</a></li>
                            <li><a href="#root">root</a></li>
                            <!--<li><a href="#tree">tree</a></li>-->
                            <li><a href="#strip_host_root">strip_host_root</a></li>
                            
                            <li class="nav-header">Treatments fonctions</li>
                            <li><a href="#resume">resume</a></li>
                            <li><a href="#keywords">keywords</a></li>
                            <!--<li><a href="#strip_script">strip_scripts</a></li>-->
                            
                            <li class="nav-header">Compatibility functions</li>
                            <li><a href="#strip_magic_quotes">strip_magic_quotes</a></li>
                            <li><a href="#get_mime_type">get_mime_type</a></li>
                            <li><a href="#strstr_before">strstr_before</a></li>
                            <!--<li><a href="#mysqlib">MySQLib</a></li>-->
                        </ul>
                    </div>
                    
                </div>
                
                <div class="span7">
                    
                    <div id="introdution">
                        <h1>Tools</h1>
                        
                        <p>
                            Tools are by default in WOK framework because we think that you need them. They are contained in :
                        </p>
                        
                        <ul>
                            <li>/core/utilities.php</li>
                            <li>/core/compatibility.php</li>
                            <li>/core/treatments.php</li>
                        </ul>
                        
                        <p>
                            These file are loaded in the init.php file. We don't advice you to edit one of these tools. You can add some in the <a href="<?php echo path('libraries'); ?>">libraries</a>.
                        </p>
                    </div>
                    
                    
                    <h2>Elementary tools</h2>
                    
                    <hr class="separator" />
                    
                    <div id="path">
                        <ul class="nav nav-tabs" data-tool="path">
                            <li class="default active"><a href="#" data-open="default">path</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                The path() function is one of the most used function. It give you the absolute path to the given parameter.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    echo path('/the-folder/the-page'); // <?php echo path('/the-folder/the-page'); ?> 
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function requires the <i>SITE_ADDR</i> constant (/core/settings.php)
                            </p>
                            
                        </div>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="root">
                        <ul class="nav nav-tabs" data-tool="root">
                            <li class="default active"><a href="#" data-open="default">root</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                The root() function give you the absolute server path to the given path.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    echo root('/the-folder/the-page'); // <?php echo root('/the-folder/the-page'); ?> 
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function requires the <i>SERVER_ROOT</i> constant (/core/init.php)
                            </p>
                            
                        </div>
                    </div>
                    
                    
                    <hr class="separator" />
                    
                    <div id="strip_host_root">
                        <ul class="nav nav-tabs" data-tool="strip_host_root">
                            <li class="default active"><a href="#" data-open="default">strip_host_root</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                Unlike to the previous functions, strip_host_root() is used only once in WOK framework. It allow to get the website URL without the project folders.<br />   
                                In the template files, we advice you to use <i>$GLOBALS['_GET']['REQUEST']</i> or <i>get_request()</i>.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    echo strip_host_root('/wok/tools'); // <?php echo strip_host_root('/wok/tools'); ?> 
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function requires the <i>SITE_ADDR</i> constant (/core/settings.php)
                            </p>
                            
                        </div>
                    </div>
                    
                    <hr class="separator" />
                    
                     <h2>Treatments functions</h2>
                    
                    
                     <hr class="separator" />
                    
                    <div id="resume">
                        <ul class="nav nav-tabs" data-tool="resume">
                            <li class="default active"><a href="#" data-open="default">resume</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                This function allow you to get a resume from a string. 
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    $text = 'This is a very long and borring text and my visitors would like to see only some works.';
    echo resume($text, 5); // <?php echo resume('This is a very long and borring text and my visitors would like to see only some works', 5); ?> 
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function requires nothing. Enjoy !
                            </p>
                            
                        </div>
                    </div>
                    
                    
                    <hr class="separator" />
                    
                    <div id="keywords">
                        <ul class="nav nav-tabs" data-tool="keywords">
                            <li class="default active"><a href="#" data-open="default">keywords</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                There are some functions we are really prude of. keywords() is one of them : just give a text, you got keywords as close as possible to those of the Google algorythm.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    $text = 'Lorem ipsum dolor ...'; // Just think this is a really big lorem ipsum text
    
    // The following are those by default. We suggest do don't change them.
    $sensitivity = 4; // Min keywords letters. Allow to exclude common words (eg.: of, a, the, ...)
    $min = 2; // Min keywords percentage
    $max = 8; // Max keywords percentage
    $limit = 10; // Max keywords number
    
    // Only the $text parameter is required
    $keywords =  keywords($text, $sensitivity, $min, $max, $limit);``
    // Or to check with the default parameters
    $keywords =  keywords($text);

    // Return an array by default. We used the implode function to print them.
    // <?php echo implode(', ', keywords('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a purus laoreet, vestibulum nulla eget, interdum sapien. Integer auctor ullamcorper erat vitae eleifend. Fusce at volutpat purus. Mauris sollicitudin orci at adipiscing scelerisque. Integer auctor at nunc at imperdiet. Etiam pharetra elit malesuada cursus vulputate. Cras posuere vel metus sit amet hendrerit. Nullam elementum ligula sem, a ultricies ligula placerat non. Cras id pretium nibh. Phasellus leo lorem, viverra eget fermentum vitae, accumsan eget nulla. Interdum et malesuada fames ac ante ipsum primis in faucibus. In laoreet vel neque nec fringilla. Mauris lorem tellus, molestie eget nunc ac, tempus varius quam. Pellentesque vestibulum rutrum est, eu semper ligula suscipit et. Integer sed mollis mi. Sed porta arcu sed ligula convallis feugiat. Nulla facilisi. Sed eleifend vehicula eros at ornare. Suspendisse elit eros, cursus et augue in, suscipit fermentum quam. Vivamus et diam porttitor nibh convallis vehicula vitae sed risus. Vivamus eu tincidunt urna. Sed dignissim consectetur lacus, vitae volutpat sem ultrices non. Interdum et malesuada fames ac ante ipsum primis in faucibus. In rhoncus quam urna, at lacinia nibh sollicitudin sed. Morbi a accumsan tortor. Sed luctus sem nec nibh sagittis convallis. Quisque elementum, dolor id ornare tempus, risus sem pharetra tortor, vitae dictum metus ipsum vitae dolor. Mauris iaculis vehicula massa vitae iaculis. Proin venenatis, nisi ut blandit laoreet, lorem lectus congue urna, sit amet adipiscing mi est ut nibh. Duis tortor tellus, feugiat et justo vitae, pharetra ultricies libero. Sed eu pellentesque velit. Maecenas eget ultricies nibh. Nullam eget nunc egestas, porta diam nec, dapibus lacus. Nulla venenatis metus ante, eget sodales ante bibendum vitae. Donec non libero sit amet orci molestie suscipit a in tortor. Maecenas posuere, lectus pellentesque venenatis porta, erat enim bibendum nisl, non eleifend erat metus vel lectus. Nullam quis massa imperdiet leo imperdiet condimentum. Cras accumsan justo nec eros molestie, sit amet consectetur augue interdum. Quisque vitae dignissim nibh, ut dapibus diam. Nunc venenatis quam nec purus rutrum, eu eleifend leo euismod. Curabitur quis egestas orci. Pellentesque sollicitudin ante augue. Etiam aliquet purus nec scelerisque rutrum. Etiam faucibus odio ligula, sit amet congue purus venenatis ac. Duis ultrices velit massa, a semper metus pulvinar id. Nullam placerat lacus nec odio faucibus semper. Sed sed gravida justo. Vivamus ullamcorper erat sit amet lobortis lacinia. Morbi sit amet lacus a enim lacinia rutrum ac non augue. Proin bibendum, magna sit amet consequat rhoncus, magna odio commodo felis, sodales ullamcorper elit arcu placerat nisi. Curabitur est turpis, laoreet pharetra purus in, eleifend convallis mauris. Duis laoreet tempus nulla.')); ?> 
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function require nothing.<br />
                                You should have a check on the keywords before using it for public posts.
                            </p>

                        </div>
                    </div>
                    
                    <hr class="separator" />
                    
                    
                    <!--
                   
                    
                    <div id="strip_script">
                        <ul class="nav nav-tabs" data-tool="strip_script">
                            <li class="default active"><a href="#" data-open="default">resume</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                This function exclude script actions from a string. 
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    $text = '&lt;a href="#mylink" onclick="alert('I am a borring pop-up');"&gt;Hey how are you ?&lt;a&gt;';
    echo strip_scripts($text); // <?php echo resume('<a href="#mylink" onclick="alert(\'I am a borring pop-up\');">Hey how are you ?<a>'); ?> 
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function requires nothing. Enjoy !
                            </p>
                            
                        </div>
                    </div>
                    
                    
                    <hr class="separator" />
                    -->
                    
                    <h2>Compatibility functions</h2>
                    
                    <p>The following function are made to have a better compatibility with any version of PHP and server configuration.</p>
                    
                    <hr class="separator" />
                    
                    <div id="strip_magic_quotes">
                        <ul class="nav nav-tabs" data-tool="strip_magic_quotes">
                            <li class="default active"><a href="#" data-open="default">strip_magic_quotes</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                Event if the magic are deleted from PHP 5.4.0, this function allow to delete them in PHP &lt; 5.4.0 from a variable or and array.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    $striped = strip_magic_quotes($_POST['fieldname']); // Strip magic quotes of a POST field request
    $striped = strip_magic_quotes($_POST); // Strip magic quotes of all POST field request
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function requires nothing. Enjoy !
                            </p>
                            
                        </div>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="get_mime_type">
                        <ul class="nav nav-tabs" data-tool="get_mime_type">
                            <li class="default active"><a href="#" data-open="default">get_mime_type</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                Get the MIME type from a file changed every PHP version. That's why we built this function which will return the file MIME type whatever your PHP version is.
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    echo get_mime_type('/template/img/background.jpg'); // <?php echo get_mime_type(root('/template/img/background.jpg')); ?> 
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function requires nothing. Enjoy !
                            </p>
                            
                        </div>
                    </div>
                    
                    
                    <hr class="separator" />
                    
                    <div id="strstr_before">
                        <ul class="nav nav-tabs" data-tool="strstr_before">
                            <li class="default active"><a href="#" data-open="default">strstr_before</a></li>
                            <li><a href="#" data-open="notes">Notes</a></li>
                        </ul>
                        
                        <div class="tab default">
                            
                            <p>
                                strstr() now allow a third parameter which allow to get the first occurence from the string parameter. This parameter is added in PHP 5.3.0. You will now could do the same if your are in a less version thanks to strstr_before().
                            </p>
                            
                            <pre><code class="language-php">&lt;?php 
    echo strstr_before('myname@domain.net', '@'); // <?php echo strstr_before('myname@domain.net', '@'); ?> 
?&gt;</code></pre>
                        </div>
                        
                        <div class="tab notes hidden">
                        
                            <p>
                                This function requires nothing. It also works if your PHP version is higher or egual to 5.3.0 !
                            </p>
                            
                        </div>
                    </div>
                    
                    
                    <hr class="separator" />
                    
                </div>
            
            </div>
           
            
        </div>
    
    </body>
    

</html>