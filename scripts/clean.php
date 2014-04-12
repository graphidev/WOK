<?php // Remove temporary files
    
    require_once 'cli.php';
    

    /**
     * Ask for an option
    **/
    if(empty($_args)):  
        echo "Illegal options number\n";
        echo "  -cache          Remove cache files\n";
        echo "  -logs           Remove logs\n";
        echo "  -tmp            Remove temporary files (manifest, locales, ...)\n";
        echo "  -all            Remove all temporary files (cache, logs, ...)\n";
        exit;
    endif;
    
    
    function remove($tmp, $path) {
        
        foreach($tmp as $name => $value) {
            if(is_array($value)):
                remove($value, "$path/$name");
                $deleted = @rmdir("$path/$name");
            elseif($name != '.htaccess'):
                $deleted = @unlink("$path/$name");
            endif;
            
            if(isset($deleted)):
                if($deleted)
                    echo '[DELETED] '.str_replace(ACCESS_PATH, '', $path)."/$name\n";
                else
                    echo '[ERROR] '.str_replace(ACCESS_PATH, '', $path)."/$name\n";
            endif;
        }
    }


    /**
     * Clean tmp folder
    **/
    if(in_array('-tmp', $_args) || in_array('-all', $_args)): 
        $tmp = explore(ACCESS_PATH.PATH_TMP);
        
        echo "Clean ".PATH_TMP." ...\n";
        if(!empty($tmp)):
            remove($tmp, ACCESS_PATH.PATH_TMP);
            echo "\n\n";
        endif;
        
    endif;


    /**
     * Clean logs folder
    **/
    if(in_array('-logs', $_args) || in_array('-all', $_args)): 
        
        $tmp = explore(ACCESS_PATH.PATH_LOGS);
        
        echo "Clean ".PATH_LOGS." ...\n";
        if(!empty($tmp)):
            
            remove($tmp, ACCESS_PATH.PATH_LOGS);
            echo "\n\n";
        endif;
        
        
    endif;

    
    /**
     * Clean cache folder
    **/
    if(in_array('-cache', $_args) || in_array('-all', $_args)): 
        
        $tmp = explore(ACCESS_PATH.PATH_CACHE);

        echo "Clean ".PATH_CACHE." ...\n";
        if(!empty($tmp)):
            remove($tmp, ACCESS_PATH.PATH_CACHE);
            echo "\n\n";
        endif;
        
        
    endif;


    echo "Cleaning is complete\n";

?>