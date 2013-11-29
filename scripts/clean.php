<?php

    /**
     * Clean all temporary files
    **/

    if(!defined('ACCESS_PATH'))
        exit("* Call as : php cli.php [script] [args]");

    function remove($tmp, $path) {
        
        foreach($tmp as $name => $value) {
            if(is_array($value)):
                remove($value, "$path/$name");
                $deleted = @rmdir("$path/$name");
            else:
                $deleted = @unlink("$path/$name");
            endif;
            
            if($deleted)
                echo '[DELETED] '.str_replace(ACCESS_PATH, '', $path)."/$name\n";
            else
                echo '[ERROR] '.str_replace(ACCESS_PATH, '', $path)."/$name\n";
        }
    }

    echo "\n[WOK cleaner]\n";

    /**
     * Ask for an option
    **/
    if(empty($_opts)):
        $input = input("Which folders (separate with a space) ?  [all|(tmp|logs|uploads)] :");
        $folders = explode(' ', $input);
        foreach($folders as $i => $folder) {
            $_opts[] = "-$folder";
        }
    endif;
    
    /**
     * Clean tmp folder
    **/
    if(in_array('-tmp', $_opts) || in_array('-all', $_opts)): 
        $tmp = tree(ACCESS_PATH.PATH_TMP);
    
        if(!empty($tmp)):
            echo "Clean ".PATH_TMP." :\n";
            remove($tmp, ACCESS_PATH.PATH_TMP);
            echo "\n\n";
        endif;
        
    endif;

    /**
     * Clean tmp folder
    **/
    if(in_array('-logs', $_opts) || in_array('-all', $_opts)): 
        
        $tmp = tree(ACCESS_PATH.PATH_LOGS);
    
        if(!empty($tmp)):
            echo "Clean ".PATH_LOGS." :\n";
            remove($tmp, ACCESS_PATH.PATH_LOGS);
            echo "\n\n";
        endif;
        
        
    endif;


    /**
     * Clean uploads folder
    **/
    if(in_array('-uploads', $_opts) || in_array('-all', $_opts)): 
        
        $tmp = tree(ACCESS_PATH.PATH_TMP_FILES);
    
        if(!empty($tmp)):
            echo "Clean ".PATH_LOGS." :\n";
            remove($tmp, ACCESS_PATH.PATH_TMP_FILES);
            echo "\n\n";
        endif;
        
    endif;

    echo "Temporary folders cleaned\n";

?>