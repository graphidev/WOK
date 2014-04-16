<?php // Install WOK
    require "cli.php";

    /**
     * Check PHP version
    **/
    if(PHP_VERSION < 5.3)
        exit("ERROR :: You must updgrade your PHP version to 5.3  or newer\n\n");
    

    function setSetting($name, $value, $settings) {
        $settings = preg_replace("#const $name (.+)?= '(.+)?';#", "const $name $1= '$value';", $settings);
        echo "  $name : $value\n";
        return $settings;
    }
    

    /**
     * Calculate default settings
    **/
    $protocol = 'http://';
    $domain = php_uname('n');
    $directory = str_replace(dirname(SYSTEM_ROOT), '', SYSTEM_ROOT);
    $timezone = @date_default_timezone_get();
    $languages = array('en_EN');
    $url = $protocol.$domain.$directory;
    
    // Customize configuration
    if(!in_array('--auto', $_args)):
        /**
         * Customize settings
        **/
        $manual = strtoupper(input("Would you want to customize your set up informations ? (Y/N) : "));
        if(empty($manual) || $manual == 'Y'):
            
            echo "* Press enter (empty value) to get the calculated value\n";
            
            $input_protocol = input("-- Default access protocol [http/https] > ");
            $input_domain = input("-- Default access domain [$domain] > ");
            $input_directory = input("-- Default access subdirectory [$directory] > ");
            $input_timezone = input("-- Server timezone [$timezone] > ");   
            
            $timezone = (!empty($input_timezone) ? $input_timezone : $timezone);
            $protocol = (!empty($input_protocol) ? $input_protocol.'://' : $protocol);
            $domain = (!empty($input_domain) ? $input_domain : $domain);
            $directory = (!empty($input_directory) ? $input_directory : $directory);
            
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

        endif;
    
        $url = $protocol.$domain.$directory;
        
        echo "\n";
    
    endif;

    /**
     * Save configuration
    **/
    echo "Generate configuration file ...\n";
    $settings = file_get_contents(ACCESS_PATH.PATH_VAR.'/settings-default.php');

    $settings = setSetting('SYSTEM_PROTOCOL', $protocol, $settings);
    $settings = setSetting('SYSTEM_DOMAIN', $domain, $settings);
    $settings = setSetting('SYSTEM_DIRECTORY', $directory, $settings);
    $settings = setSetting('SYSTEM_TIMEZONE', $timezone, $settings);
    $settings = setSetting('SYSTEM_LANGUAGES', implode(' ', $languages), $settings);

    $settings = setSetting('SESSIONS_SALT', substr(sha1(uniqid('sess_')), -20), $settings);
    $settings = setSetting('TOKENS_SALT', substr(sha1(uniqid('tok_')), -20), $settings);
    $settings = setSetting('COOKIES_SALT', substr(sha1(uniqid('cook_')), -20), $settings);
    
    if(!file_exists(ACCESS_PATH.PATH_VAR.'/settings.php')):
        $file = fopen(ACCESS_PATH.PATH_VAR.'/settings.php', 'w+');
        fclose($file);
    endif;

    file_put_contents(ACCESS_PATH.PATH_VAR.'/settings.php', $settings);

    echo "\nGenerate required files and folders ...\n";
    foreach($languages as $i => $language) {
        if(@mkdir(ACCESS_PATH.PATH_TMP))
            echo "  ".PATH_TMP."\n";
        
        if(@mkdir(ACCESS_PATH.PATH_CONTROLLERS))
            echo "  ".PATH_CONTROLLERS."\n";
        
        if(@mkdir(ACCESS_PATH.PATH_MODELS))
            echo "  ".PATH_MODELS."\n";
        
        if(@mkdir(ACCESS_PATH.PATH_LOCALES."/$language"))
            echo "  /locales/$language\n";
    }
        
    /**
     * Generate .htaccess file
    **/
    $htaccess = file_get_contents(ACCESS_PATH.'/.htaccess.default');
    $htaccess = str_replace('__WOK_DIR__', $directory, $htaccess);
     if(!file_exists(ACCESS_PATH.'/.htaccess')):
        $file = fopen(ACCESS_PATH.'/.htaccess', 'w+');
        fclose($file);
    endif;
    file_put_contents(ACCESS_PATH.'/.htaccess', $htaccess);
    echo "  /.htaccess\n";
    
    echo "\n";

    /**
     * End of WOK setup
    **/
    echo "The configuration is now ready.\n";
    echo "Thank you for considering WOK power. \n";
?>