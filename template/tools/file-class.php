<html>
    
    <head>
        <title>File class | Web Operational Kit</title>
        
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
                <li><a href="<?php echo path('tools/'); ?>">Included tools</a> <span class="divider">/</span></li>
                <li>File class</li>
            </ul>
            
            <div class="row">
            
                <div class="span3">
                    <div class="well" style="padding:0;">
                        <ul class="nav nav-list">
                            <li class="nav-header">File class</li>
                            <li class="active"><a href="#introduction">Introduction</a></li>
                            
                            <li class="nav-header">Get file methods</li>
                            <li><a href="#construct">construct</a></li>
                            <li><a href="#upload">upload</a></li>
                            <li><a href="#download">download</a></li>
                            <li><a href="#select">select</a></li>
                            
                            <li class="nav-header">Treatments methods</li>
                            <li><a href="#move">move</a></li>
                            <li><a href="#archive">archive</a></li>
                            <li><a href="#resize">resize</a></li>
                            
                            <li class="nav-header">Others methods</li>
                            <li><a href="#info">info</a></li>
                            <li><a href="#reset">reset</a></li>
                        </ul>
                    </div>
                    
                </div>
                
                <div class="span7">
                    
                    <div id="introdution">
                        <h1>File class</h1>
                        
                        <p>
                            The file class have been developed to help you to get and manage files. It queries the following functions and libraries :
                        </p>
                        
                        <ul>
                            <li>root (WOK native)</li>
                            <li>get_mime_type (WOK native)</li>
                            <li><a href="http://php.net/manual/fr/book.image.php" target="_blank">GD</a> (PHP library)</li>
                        </ul>
                        
                        <p>
                            The file class is loaded in /core/file.php<br />
                            It check and allow the following MIME type and extension :<br />
                            
                        </p>
                        <div class="alert">If the file obtained is not in the list, it will automatically be placed in a zip file for security reasons.</div>
                        <?php
                            $formats = array('image' => array('png', 'x-png', 'jpg', 'jpeg', 'pjpeg', 'gif'),
                            'text' => array('txt','plain', 'htm', 'html', 'css', 'sql', 'phps', 'js', 'json', 'cpp'),
                            'document' => array('rtf', 'odt','msword', 'doc', 'docx', 'pdf', 'xml', 'csv', 'ppt', 'pps',
                            // New Microsoft Office documents
                            'vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.template', 
                            'vnd.openxmlformats-officedocument.presentationml.template',
                            'vnd.openxmlformats-officedocument.presentationml.slideshow', 
                            'vnd.openxmlformats-officedocument.presentationml.presentation',
                            'vnd.openxmlformats-officedocument.presentationml.slide',
                            'vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'vnd.openxmlformats-officedocument.wordprocessingml.template',
                            'vnd.ms-excel.addin.macroEnabled.12',
                            'vnd.ms-excel.sheet.binary.macroEnabled.12'
                            ),
                            'video' => array('video', 'mp4', 'ogv', 'webm', 'flv', 'mpeg', 'mpg', 'mov', 'quicktime'),
                            'audio' => array('audio', 'mp3', 'ogg', 'flac', 'aac', 'mpeg', 'mpg'),
                            'archive' => array('rar', 'zip', 'gzip', 'gz', 'tar', 'tgz', '7z', 'dmg', 'iso', 'pkg', 'x-zip-compressed', 'x-rar-compressed', 'x-gzip', 'x-gunzip', 'x-tar-gz', 'x-tar', 'octet-stream'),
                            'other' => array('pkg', 'psd', 'svg', 'tiff', 'tga', 'bmp', 'wmv'));
                        ?>
                        <ul>
                            <?php 
                               foreach($formats as $type => $list) {
                                   echo '<li><b>'.strtoupper($type).'</b><ul>';
                                    
                                        foreach($list as $i => $format) {
                                            echo '<li>'.$format.'</li>';   
                                        }
                                   
                                   echo '</ul></li>';
                               }
                            ?>
                        </ul>

                    </div>
                    
                    
                    <h2>Get file methods</h2>
                    
                    <p>
                        The following methods will get the file and its informations and create a temporary file to work with.<br />
                        By default, the temporary file will be placed in /files/tmp (PATH_TMP_FILES constant)                        
                    </p>
                    
                    <hr class="separator" />
                    
                    <div id="construct">
                        <h3>Construct</h3>
                        <p>
                            The construct method will initialize the file class and help you to get the file you desire.<br />
                            If you get a file, it call one of the three next methods.
                        </p>
                        <pre><code class="language-php">&lt;?php 
    $file = new file(); // Initialize the file class
    
    $protected = false; // Make the file unaccessible by default users (chmod to 600)
    // This previous parameter is not required and its value is false by default
    $file = new file($_FILES['upload'], 'upload', $protected); // Get file by upload
    $file = new file('http://example.com/filename.ext', 'download', $protected); // Get file from URL
    $file = new file('/files/filename.ext', 'select', $protected); // Get file on server
?&gt;</code></pre>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="upload">
                        <h3>upload</h3>
                        <p>
                            The upload method get the file from a file upload. You juste have to give $_FILES index name.<br />
                            All the file upload verifications are made.
                        </p>
                        <pre><code class="language-php">&lt;?php 
    $file = new file(); // Initialize the file class
    $protected = false; // Make the file unaccessible by default users (chmod to 600)
    $file->upload($_FILES['upload'], $protected); // Get file by upload
?&gt;</code></pre>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="download">
                        <h3>download</h3>
                        <p>
                            The download method get the file from an external server thanks to the file URL.
                        </p>
                        <pre><code class="language-php">&lt;?php 
    $file = new file(); // Initialize the file class
    $protected = false; // Make the file unaccessible by default users (chmod to 600)
    $file->download('http://example.com/filename.ext', $protected); // Get file from URL
?&gt;</code></pre>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="select">
                        <h3>select</h3>
                        <p>
                            The select method get the file from the current server thanks to the file relative or absolute path.
                        </p>
                        <pre><code class="language-php">&lt;?php 
    $file = new file(); // Initialize the file class
    $protected = false; // Make the file unaccessible by default users (chmod to 600)
    $file->select('/files/filename.ext', $protected); // Get file on server
?&gt;</code></pre>
                    </div>
                    
                    <hr class="separator" />
                    
                    <h2>Treatments methods</h2>
                    
                    <p>
                        After you called one of the previous methods, you can manage your file. The operations are made on the temporary copy in order to avoid errors.
                    </p>
                    
                    <hr class="separator" />
                    
                    <div id="move">
                        <h3>move</h3>
                        <p>
                            This function allow you to copy the temporary file to a new folder.
                        </p>
                        <pre><code class="language-php">&lt;?php 
    $file = new file(); // Initialize the file class
    // Here you select your file thanks to a get method.
    $protected = false; // Make the file unaccessible by default users (chmod to 600)
    $file->move('/the/path/you/want/filename.ext', $protected); // Copy the file to first parameter
?&gt;</code></pre>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="archive">
                        <h3>archive</h3>
                        <p>
                            This function put the selected file in a ZIP archive. If you repeat this operation with the same archive file, all the files will be added and not overwrited.
                        </p>
                        <pre><code class="language-php">&lt;?php 
    $file = new file(); // Initialize the file class
    // Here you select your file thanks to a get method.
    $protected = false; // Make the file unaccessible by default users (chmod to 600)
    $filename = 'newfilename.%ext%'; // Rename the file in the archive - orginal name by default
    $file->archive('/files/archive.zip', $filename, $protected); // Copy the file in the archive
?&gt;</code></pre>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="resize">
                        <h3>resize</h3>
                        <p>
                            This functions is reserved to the image files. It allow you to resize it.
                        </p>
                        <pre><code class="language-php">&lt;?php 
    $file = new file(); // Initialize the file class
    // Here you select your file thanks to a get method.

    $width = 100; // Resize image width
    $height = 100; // Resize image height
    $destination = '/folder/filename.%ext%'; // %ext% will be replaced by the file extension
    // The following parameter will be used if the new image size is lower than the original
    $margin = true; // If it's true, the resize will be centered of the original
    $margin = array(256, 512); // If it's an array, the resize will began to the first (left) and second (right) entries 

    $file->resize($width, $height, $margin); // Resize the image
?&gt;</code></pre>
                    </div>
                    
                    <hr class="separator" />
                    
                    <h2>Other methods</h2>
                    
                    <p>
                        The file class contains some methods which will help you (again).
                    </p>
                    
                    <hr class="separator" />
                    
                    <div id="info">
                        <h3>info</h3>
                        <p>
                            This function return you the file information required. The informations you can access to are :
                        </p>
                        <ul>
                            <li><b>mime</b> - <i>File MIME type</i></li>
                            <li><b>type</b> - <i>File type (document, image, text, archive ...)</i></li>
                            <li><b>path</b> - <i>File path</i></li>
                            <li><b>filename</b> - <i>File name (with extension)</i></li>
                            <li><b>extension</b> - <i>File extension</i></li>
                            <li><b>size</b> - <i>File size (bytes)</i></li>
                            <li><b>width</b> - <i>Image width (pixels)</i></li>
                            <li><b>height</b> - <i>Image height (pixels)</i></li>
                        </ul>
                        <pre><code class="language-php">&lt;?php 
    $file = new file(); // Initialize the file class
    // Here you select your file thanks to a get method
    
    echo $file->info('mime'); // Image/png
    echo $file->info('filename'); // imagename.png
    echo $file->info('width'); // 400
    echo $file->info('height'); // 300
    // ...
?&gt;</code></pre>
                    </div>
                    
                    <hr class="separator" />
                    
                    <div id="reset">
                        <h3>reset</h3>
                        <p>
                            This function reset the file class and it parameters. It also remove the temporary file.<br />
                        </p>
                        <div class="alert">You have to call this function each time you use the file class !</div>
                        
                        <pre><code class="language-php">&lt;?php 
    $file = new file(); // Initialize the file class
    // Here you select your file thanks to a get method
    // Here your file's operations
    $file->reset();
?&gt;</code></pre>
                    </div>
                                                   
                </div>
                
                
           
            
        </div>
    
    </body>
    

</html>