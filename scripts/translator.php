<?php
    
    header('Content-Type: text/plain');

    if(!function_exists('tree')):
        function tree($dir) {
            $handle = opendir($dir);
            $array = array();
            
            while(false !== ($entry = readdir($handle))):
                $entry = trim($entry);
                if(!preg_match('#^(\.|\.\.|\.DS_Store$)#is', $entry)):
                    if(is_dir("$dir/$entry")):
                        $array[$entry] = tree($dir.'/'.$entry);
                    endif;
                        
                endif;
            endwhile;
            
            rewinddir($handle);
            
            while(false !== ($entry = readdir($handle))):
                if(!preg_match('#^(\.|\.\.|\.DS_Store$)#is', $entry)):
                    if(is_file($dir.'/'.$entry)):
                        $array[$entry] = $entry;
                    endif;
                endif;
            endwhile;
            
            closedir($handle);
            
            return $array;
        }
    endif;
    
    $path = dirname(dirname(__FILE__));

    $locales = tree("$path/languages");
    
    echo "Encode to JSON ...\r\n";

    foreach($locales as $locale => $languages) {
        $language = $locale;
        echo "[$language]\r\n";
        
        foreach($languages as $folder => $sources) {
            if(is_array($sources) && $folder == 'sources'):
                foreach($sources as $file => $value) {
                    /*
                    $handle = fopen($file, 'r');
                        while (($buffer = fgets($handle)) !== false) {
                            $path = array();
                            if(substr($buffer, 0, 1) != '#'):
                                $data = explode('.', $buffer);
                                foreach($data as $index => $node) {
                                    if(!is_array($node))
                                        $array[$node] = ;
                                    else:
                                }
                            endif;
                        }
                        
                    fclose($handle)
                    */
                    
                    $locale = strstr($file, '.', true);
            
                    include("$path/languages/$language/$folder/$file");

                    echo "- $locale \r\n";
                    
                    $json = json_encode($$locale);
                    
                    if(!file_exists("$path/languages$language/$locale.json")):
                        $file = fopen("$path/languages/$language/$locale.json", 'w+');
                        fclose($file);
                    endif;
                                    
                    file_put_contents("$path/languages/$language/$locale.json", $json);
                                        
                } 
            endif;
        }
        
    }

?>