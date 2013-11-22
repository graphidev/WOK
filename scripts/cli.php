<?php

    /**
     * WOK CLI scripts call
     * Define all required informations
     * Call bootstrap file
    **/
    
    /**
     * Get required informations
    **/
    define('ACCESS_PATH', dirname(dirname(__FILE__)));
    require_once ACCESS_PATH . "/core/init.php";
    
    // CLI input function
    function input($string) {
        echo $string;
        $handle = fopen("php://stdin","r");
        $data = trim(fgets($handle));
        if($data == 'exit')
            exit;
        else
            return $data;
    }
    
    /**
     * Existing script
    **/
    if(!empty($argv[1]) && file_exists(ACCESS_PATH.'/scripts/'.$argv[1].'.php')):
        
        $_opts = array_slice($argv, 2); // define options

        include(ACCESS_PATH.'/scripts/'.$argv[1].'.php'); exit;
    
    /**
     * Not called script
    **/
    else:

        echo "* php cli.php [script] [args]\n";
        echo "* Available scripts :\n";
        $scripts = scandir(ACCESS_PATH.'/scripts');
        foreach($scripts as $i => $name) {
            if(substr($name, -4) == '.php' && $name != 'cli.php')
                echo '* - '.substr($name, 0, -4)."\n";
        }
        exit;

    endif;

?>