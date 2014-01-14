<?php

    /**
     * WOK CLI boot scripts
     * Define all required informations
    **/
    define('ACCESS_PATH', dirname(dirname(__FILE__)));
    require_once ACCESS_PATH . "/core/init.php";
    
    $_args = array_slice($argv, 1);

    
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
     * CLI usage
    **/
    if($argv[0] == pathinfo(__FILE__, PATHINFO_BASENAME)):

        if(!empty($argv[1]) && $argv[1] == '-v'):
            echo WOK_VERSION.' '.WOK_EXTRA_RELEASE."\r\n";
    
        elseif(!empty($argv[1]) && $argv[1] == '-p'):
            echo constant('PATH_'.strtoupper($argv[2]));
        
        
    
        elseif(!empty($argv[1]) && $argv[1] == '-l'):
            $scripts = scandir(ACCESS_PATH.'/scripts');
            foreach($scripts as $i => $name) {
                if(substr($name, -4) == '.php' && $name != 'cli.php'):
                    $script = fopen($name, 'r');
                    $description = trim(preg_replace("#^<\?php ?(//(.+))?$#isU", '$2', fgets($script, 4096)));
                    
                    //echo '  '.substr($name, 0, -4)."        $description\n";
                    
                    printf("    %s           %-50.50s\r\n", substr($name, 0, -4), $description);
                
                endif;
            }
            exit;   
               
        else:
            exit("Illegal call\n");
    
    
        endif;

    endif;

?>