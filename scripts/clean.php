<?php

    /**
     * Clean all temporary files
    **/
    define('ACCESS_PATH', dirname(dirname(__FILE__)));

    require_once ACCESS_PATH . "/core/init.php";

    function remove($tmp, $path) {
        
        foreach($tmp as $name => $value) {
            if(is_array($value)):
                remove($value, "$path/$name");
                $deleted = rmdir("$path/$name");
            else:
                $deleted = @unlink("$path/$name");
            endif;
            
            if($deleted)
                echo '[DELETED] '.str_replace(ACCESS_PATH, '', $path)."/$name\n";
            else
                echo '[ERROR] '.str_replace(ACCESS_PATH, '', $path)."/$name\n";
        }
    }

    echo "\nCleaning ".PATH_TMP." ...\n\n";

    $tmp = tree(ACCESS_PATH.PATH_TMP);

    if(!empty($tmp))
        remove($tmp, ACCESS_PATH.PATH_TMP);
    
    else
        exit("/!\ Temporary directory is still empty.\n\n");

    echo "\nDone !\n\n";

?>