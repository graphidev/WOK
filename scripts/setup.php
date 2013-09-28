<?php
    
    define('ACCESS_PATH', dirname(dirname(__FILE__)));

    require ACCESS_PATH . "/core/init.php";

    function input($string) {
        echo $string;
        $handle = fopen("php://stdin","r");
        $data = trim(fgets($handle));
        if($data == 'exit')
            exit("[WOK setup aborted]\n\n");
        else
            return $data;
    }

    function setSetting($name, $value, $settings) {
        $settings = preg_replace("#const $name (.+)?= '(.+)?';#", "const $name $1= '$value';", $settings);
        echo "-- -- $name : $value\n";
        return $settings;
    }
    
    
    /**
     * WOK ASCII name
    **/
    echo  "
 __          ______  _  __       
 \ \        / / __ \| |/ /      
  \ \  /\  / / |  | | ' /       
   \ \/  \/ /| |  | |  <        
    \  /\  / | |__| | . \       
     \/  \/   \____/|_|\_\      
    \n";


    echo "\n[Setup WOK " . WOK_VERSION . "]\n";
    echo "* The following program will help you to configure WOK\n";
    echo "* Please type 'exit' in any time if you want to stop it\n\n";

    /**
     * Check PHP version
    **/
    if(PHP_VERSION < 5.3)
        exit("ERROR :: You must updgrade your PHP version to 5.3 at least\n\n");
    
    /**
     * Check required folders
    **/
    if(!@is_writable(ACCESS_PATH.PATH_VAR) || 
       !@is_writable(ACCESS_PATH.PATH_TMP) || 
       !@is_writable(ACCESS_PATH.PATH_LOGS) || 
       !@is_writable(ACCESS_PATH.PATH_FILES) || 
       !@is_writable(ACCESS_PATH.PATH_TMP_FILES)):
        echo "ERROR :: The following folders must be accesible for writing :\n";
        echo "- " . PATH_VAR . "\n";
        echo "- " . PATH_TMP . "\n";
        echo "- " . PATH_LOGS . "\n";
        echo "- " . PATH_FILES . "\n";
        echo "- " . PATH_TMP_FILES . "\n";
        exit("\n");
    endif;

    /**
     * Try to calculate default settings
    **/
    $protocol = 'http://';
    $domain = php_uname('n');
    $directory = str_replace(dirname(SYSTEM_ROOT), '', SYSTEM_ROOT);
    $timezone = @date_default_timezone_get();

    /**
     * Customize settings
    **/
    $manual = strtoupper(input("Would you customize your setup ? (Y/N) : "));
    if(empty($manual) || $manual == 'Y'):
        
        echo "* Press enter (empty value) to get the calculated value\n";
        
        $input_protocol = input("-- Default access protocol [http/https] > ").'://';
        $input_domain = input("-- Default access domain [$domain] > ");
        $input_directory = input("-- Default access subdirectory [$directory] > ");
        $input_timezone = input("-- Server timezone [$timezone] > ");   
        
        $timezone = (!empty($input_timezone) ? $input_timezone : $timezone);
        $protocol = ($input_protocol != '://' ? $input_protocol.'://' : $protocol);
        $domain = (!empty($input_domain) ? $input_domain : $domain);
        $directory = (!empty($input_directory) ? $input_directory : '/');
    
    endif;

    $url = $protocol.$domain.$directory;
    
    echo "\n";
    


    /**
     * Configure languages
    **/
    echo "Which languages are supported in your project ?\n";
    echo "* The languages codes must correspond to the ISO 639 norm (country_LANGUAGE)\n";
    echo "* The program will ask you a new language until you press Enter (empty value)\n";
    echo "* If you won't define any language, the 'en_EN' code will be applied\n";
        
    $languages = array();
    $new = true;
    while(strtolower($new) != '') {
        $new = input("-- Add language > ");
        if(!empty($new))
            $languages[] = $new;
    }
    if(count($languages) == 0)
        $languages = array('en_EN');
    
    echo "\n";
    

    /**
     * Save configuration
    **/
    echo "Generate configuration file ...\n";
    $settings = file_get_contents(ACCESS_PATH.PATH_VAR.'/settings-default.php');

    $settings = setSetting('SYSTEM_DEFAULT_PROTOCOL', $protocol, $settings);
    $settings = setSetting('SERVER_DOMAIN', $domain, $settings);
    $settings = setSetting('SYSTEM_DIRECTORY_PATH', $directory, $settings);
    $settings = setSetting('SYSTEM_TIMEZONE', $timezone, $settings);
    $settings = setSetting('SYSTEM_ACCEPT_LANGUAGES', implode(',', $languages), $settings);

    echo "\nGenerate crypt salts ...\n";
    $settings = setSetting('SESSION_CRYPT', sha1(uniqid('sess_')), $settings);
    $settings = setSetting('TOKEN_SALT', sha1(uniqid('tok_')), $settings);
    $settings = setSetting('COOKIE_CRYPT', sha1(uniqid('cook_')), $settings);
    
    if(!file_exists(ACCESS_PATH.PATH_VAR.'/settings.php')):
        $file = fopen(ACCESS_PATH.PATH_VAR.'/settings.php', 'w+');
        fclose($file);
    endif;

    file_put_contents(ACCESS_PATH.PATH_VAR.'/settings.php', $settings);

    echo "\nCreate locales folders (if necessary) ...\n";
    foreach($languages as $i => $language) {
        if(@mkdir(ACCESS_PATH.PATH_LOCALES."/$language"))
            echo "-- /languages/$language\n";
    }
    
    echo "\n";
    
    /**
     * Generate .htaccess file
    **/
    $htaccess = file_get_contents(ACCESS_PATH.'/.htaccess.default');
    $htaccess = str_replace('__WOK_DIR__', ($directory != '/' ? $directory : null), $htaccess);
     if(!file_exists(ACCESS_PATH.'/.htaccess')):
        $file = fopen(ACCESS_PATH.'/.htaccess', 'w+');
        fclose($file);
    endif;
    file_put_contents(ACCESS_PATH.'/.htaccess', $htaccess);
    echo "Generate .htaccess file ...\n";
    
    echo "\n";

    /**
     * End of WOK setup
    **/
    echo "* We are happy yo say that the configuration is done (if no errors appears).\n";
    echo "* We really hope that you will like WOK and use it for many of your projects. \n";

    exit("[/Setup WOK " . WOK_VERSION . "]\n\n");
?>