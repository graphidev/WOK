<?php
    /**
     * @package Libraries
    **/
    class Tpl {
        
        public static function zone($path, $transmit = array()) {
            $path = root(PATH_TEMPLATES."/$path.php");
            if(file_exists($path)):
                extract($transmit);
                include($path);
            else:
                trigger_error("Can't call zone '$path'", E_USER_NOTICE);
            endif;
        }
        
        public static function headers($transmit = array()) {
            self::zone('inc/headers', $transmit);
        }
        
        public static function banner($transmit = array()) {
            self::zone('inc/banner', $transmit);   
        }
        
        public static function footer($transmit = array()) {
            self::zone('inc/footer', $transmit);   
        }
        
        public static function sidebar($transmit = array()) {
            self::zone('inc/sidebar', $transmit);   
        }
        
    }

?>