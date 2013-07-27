<?php

    require "../core/init.php";

    header('Content-Type: text/plain');
    
    $locales = tree(root(PATH_LOCALES));

    foreach($locales as $locale => $languages) {
        $language = $locale;
        echo "[$language]\r\n";
        
        foreach($languages as $folder => $sources) {
            if(is_array($sources)):
                foreach($sources as $file => $value) {
                    
                    include(root(PATH_LOCALES."/$language/$folder/$file"));
                    
                    $locale = strstr_before($file, '.');
                    
                    echo "- $locale \r\n";
                    
                    $json = json_encode($data);
                    
                    if(!file_exists(root(PATH_LOCALES."/$language/$locale.json"))):
                        $file = fopen(root(PATH_LOCALES."/$language/$locale.json"), 'w+');
                        fclose($file);
                    endif;
                                    
                    file_put_contents(root(PATH_LOCALES."/$language/$locale.json"), $json);
                                        
                } 
            endif;
        }
        
    }

?>