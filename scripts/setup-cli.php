<?php
    
    require dirname(dirname(__FILE__)) . "/core/init.php";

    function input($string) {
        echo $string;
        $handle = fopen ("php://stdin","r");
        $data = trim(fgets($handle));
        if($data == 'exit')
            exit("[WOK setup aborted]\n");
        else
            return $data;
    }

    function setSetting($name, $value, $settings) {
        $settings = preg_replace("#define\('$name', '(.+)?'\)#", "define('$name', '$value')", $settings);
        echo "-- -- $name : $value\n";
        return $settings;
    }
    

//    system('git clone https://github.com/graphidev/WOK.git test-clone-from-php');
//    exit();

    if(PHP_VERSION < 5.1)
        exit("[WOK setup] You must updgrade your PHP version to 5.1 at least\n");
        
    echo "[Setup WOK " . WOK_VERSION . "]\n";

    echo "Looking for updates ... \n";
    
    $distant = str_replace(array('.',':'), '', file_get_contents('http://www.graphidev.fr/lab/wok-version'));
    if(str_replace(array('.',':'), '', WOK_VERSION) < $distant)
        $uptodate = true;
    else
        $uptodate = false;

    if(!$uptodate):
       if(input("You're WOK version is not up to date. Abort ? (Y/N) : ") == 'Y'):
           exit("[WOK setup aborted]\n");
        endif;
    else:
        echo "-- You're WOK version is up to date.\n\n"; 
    endif;
    
    if(strtolower(input("Would you customize your setup ? (Y/N) : ")) == 'y'):
        
        $subdirectory = str_replace(dirname(SYSTEM_ROOT), '', SYSTEM_ROOT);
        
        $protocol = input("-- Default access protocol [http/https] > ").'://';
        $domain = input("-- Default access domain [".php_uname('n')."] > ");
        $directory = input("-- Default access subdirectory [$subdirectory] > ");
        $timezone = input("-- Server timezone [".date_default_timezone_get()."] > ");   
        
        if(empty($timezone)) $timezone = date_default_timezone_get();
        if($protocol == '://') $protocol = 'http://';
        if(empty($domain)) $domain = php_uname('n');
        
        $url = $protocol.$domain.$directory;
    
    else:
        
        $protocol = 'http://';
        $domain = php_uname('n');
        $directory = str_replace(dirname(SYSTEM_ROOT), '', SYSTEM_ROOT);
        $timezone = date_default_timezone_get();
        $url = $protocol.$domain.$directory;
        

    endif;

    echo "-- Accept languages [en_EN, ...]\n";
    $languages = array();
    $iso = true;
    while(strtolower($iso) != '') {
        $iso = input("-- -- Add language (just press Enter to finish) > ");
        if(!empty($iso))
            $languages[] = $iso;
    }
    if(count($languages) == 0)
        $languages = array('en_EN');
    
    echo "\n";

    if(!file_exists($url.'/index.php')):
        //exit("[WOK] Framework not found in $url\n");
    endif;
    

    // SAVE INPUTS
    echo "Save settings ...\n";
    $settings = file_get_contents(SYSTEM_ROOT.'/core/settings-default.php');

    $settings = setSetting('SYSTEM_DEFAULT_PROTOCOL', $protocol, $settings);
    $settings = setSetting('SERVER_DOMAIN', $domain, $settings);
    $settings = setSetting('SYSTEM_DIRECTORY_PATH', $directory, $settings);

    $settings = setSetting('SYSTEM_TIMEZONE', $timezone, $settings);

    $settings = setSetting('SESSION_CRYPT', sha1(uniqid('sess_')), $settings);
    $settings = setSetting('TOKEN_SALT', sha1(uniqid('tok_')), $settings);
    $settings = setSetting('COOKIE_CRYPT', sha1(uniqid('cook_')), $settings);

    $settings = setSetting('SYSTEM_ACCEPT_LANGUAGES', implode(',', $languages), $settings);
    
    if(!file_exists(SYSTEM_ROOT.'/core/settings.php')):
        $file = fopen(SYSTEM_ROOT.'/core/settings.php', 'w+');
        fclose($file);
    endif;

    file_put_contents(SYSTEM_ROOT.'/core/settings.php', $settings);

    echo "\nCreate languages folders ...\n";
    foreach($languages as $i => $language) {
        if(@mkdir(SYSTEM_ROOT."/languages/$language"))
            echo "-- /languages/$language\n";
    }
    echo "\n";

    echo "[/Setup WOK " . WOK_VERSION . "]\n";
?>