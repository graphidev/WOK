<?php
    
    $locales = tree(root(PATH_LOCALES));
    $data = array();
    foreach($locales as $language => $locale) {
        foreach($locale as $translation) {
            if(!is_array($translation))
                $data[] = json_decode(file_get_contents(root(PATH_LOCALES."/$language/$translation")));
        }
    }
    header('Content-type: application/json');                              
    echo json_encode($data);
        
?>