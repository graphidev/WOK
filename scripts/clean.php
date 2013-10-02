<?php

    /**
     * Clean all temporary files
    **/
    define('ACCESS_PATH', dirname(dirname(__FILE__)));

    require_once ACCESS_PATH . "/core/init.php";
    
    $options = $argv;

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

    function input($string) {
        echo $string;
        $handle = fopen("php://stdin","r");
        $data = trim(fgets($handle));
        if($data == 'exit')
            exit;
        else
            return $data;
    }

    echo "\n[WOK cleaner]\n";

    /**
     * Ask for an option
    **/
    if(count($options) == 1):
        $input = input("Which folders (separate with a space) ?  [all|(tmp|logs|uploads)] :");
        $folders = explode(' ', $input);
        foreach($folders as $i => $folder) {
            $options[] = "-$folder";
        }
    endif;
    
    /**
     * Clean tmp folder
    **/
    if(in_array('-tmp', $options) || in_array('-all', $options)): 
        $tmp = tree(ACCESS_PATH.PATH_TMP);
    
        if(!empty($tmp)):
            echo "Clean ".PATH_TMP." :\n";
            remove($tmp, ACCESS_PATH.PATH_TMP);
            echo "\n\n";
        endif;
        
    endif;


    /**
     * Clean cache folder
    **/
    if(in_array('-cache', $options) || in_array('-all', $options)): 
        $tmp = tree(ACCESS_PATH.PATH_CACHE);
    
        if(!empty($tmp)):
            echo "Clean ".PATH_CACHE." :\n";
            remove($tmp, ACCESS_PATH.PATH_CACHE);
            echo "\n\n";
        endif;
        
    endif;

    /**
     * Clean tmp folder
    **/
    if(in_array('-logs', $options) || in_array('-all', $options)): 
        
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
    if(in_array('-uploads', $options) || in_array('-all', $options)): 
        
        $tmp = tree(ACCESS_PATH.PATH_TMP_FILES);
    
        if(!empty($tmp)):
            echo "Clean ".PATH_LOGS." :\n";
            remove($tmp, ACCESS_PATH.PATH_TMP_FILES);
            echo "\n\n";
        endif;
        
    endif;

    echo "Temporary folders cleaned\n";

?>