<html>
    
    <head>
        <title>WOK tests | Web Operational Kit</title>
        
        <?php tpl_headers(); ?>
        
    </head>
    
    <body>
        
        <div id="main">
            
            <?php tpl_banner(); ?>
            
            <div class="content">
                                
                <?php

                        echo '<h1>Downloading file ...</h1>';

                        $file = new file();

                        //if(!empty($GLOBALS['_POST']['file_url'])):
                       /*     echo '<p>Download file from URL : http://www.graphidev.fr/template/img/home/w3c-browser.png</p>';
                        
                            if($file->download('http://www.graphidev.fr/template/img/home/w3c-browser.png')):
                                
                                echo '<div class="alert alert-success">File downloaded.</div>';
                                echo '<img src="http://www.graphidev.fr/template/img/home/w3c-browser.png" alt="original" />';

                                $width = $file->info('width');
                                $height = $file->info('height');
                                
                                $width = 50*$width/100;
                                $height = 50*$height/100;
                                $width = 100;
                                $height = 100;
                                $destination = root(PATH_TMP_FILES.'/test.'.$file->info('extension'));
                                $origin = array(40,40);

                                $file->resize($width, $height, $destination, $origin);

                                echo '<img src="'.path(PATH_TMP_FILES.'/test.'.$file->info('extension')).'" alt="test" />';
                                $file->reset();
                        
                            else:
                                echo '<div class="alert alert-error">Error while downloading file.</div>';
                            endif;
                        */
                            $file = new file();
                            $file->select(PATH_TMP_FILES.'/test.png');
                            $file->archive(PATH_TMP_FILES.'/archive.zip', 'test.png');
                            $file->reset();
                            
                            $file = new file();
                            $file->select(PATH_TMP_FILES.'/f1.png');
                            $file->archive(PATH_TMP_FILES.'/archive1.zip', 'background.png');
                            $file->reset();
                            
                        /*
                            $file->select(root(PATH_TMP_FILES.'/f2.jpg'));
                            $file->archive(root(PATH_TMP_FILES.'/test.zip'), 'bidule2.jpg');
                            $file->reset();

                            $file->select(root(PATH_TMP_FILES.'/f3.jpg'));
                            $file->archive(root(PATH_TMP_FILES.'/test.zip'), 'bidule3.jpg');
                            $file->reset();
                        */
                ?>
                
            </div>
            
        </div>
    
    </body>
    

</html>