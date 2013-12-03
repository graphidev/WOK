<?php
    
    /**
     * Use the right way to call this script
    **/
    if(!defined('ACCESS_PATH'))
        exit("* Call as : php cli.php [script] [args]\n");

    /**
     * Has informations
    **/
    if(empty($_opts) || in_array($_opts[0], array('-h', '--help', '?'))):
        // Global informations
        echo "Here are the options you can call :\n";
        echo "  -v                  Get current WOK version\n";
        //echo "  -u                  Check for updates\n";
        echo "  -w                  Get WOK system folders pathes\n";
        
        // Libraries
        //echo "  -l                  Get the available libraries \n";
        //echo "  -l -w               Get the external libraries list\n";
        //echo "  -l -d [name]        Download an external library\n";
        exit;
    endif;
    
    const GITHUB_API = 'https://api.github.com/repose/graphidev/WOK/releases';

    /**
     * Get WOK version
    **/
    if($_opts[0] == '-v')
        echo WOK_VERSION.' '.WOK_EXTRA_RELEASE."\n";
    

    /**
     * Check for updates 
    **/
    if($_opts[0] == '-u'):
        $curl = new Curl(GITHUB_API);
        echo $curl->response();
    endif;


    /**
     * Get all the WOK pathes
    **/
    if($_opts[0] == '-w'):
        echo "[ROOT] ".ACCESS_PATH."\n";
        echo "  [CORE]          ".PATH_CORE."\n";
        echo "  [CONFIG]        ".PATH_VAR."\n";
        echo "  [LOGS]          ".PATH_LOGS."\n";
        echo "  [TMP]           ".PATH_TMP."\n";
        echo "  [FILES]         ".PATH_FILES."\n";
        echo "  [TMP FILES]     ".PATH_FILES."\n";
        echo "  [LIBRARIES]     ".PATH_LIBRARIES."\n";
        echo "  [LOCALES]       ".PATH_LOCALES."\n";
        echo "  [CONTROLLERS]   ".PATH_CONTROLLERS."\n";
        echo "  [TEMPLATES]     ".PATH_LOCALES."\n";
        echo "  [CACHE]         ".PATH_CACHE."\n";
    endif;
    
    /**
     * WOK libraries
    **/
    if($_opts[0] == '-l'):

        

    endif;
    
?>