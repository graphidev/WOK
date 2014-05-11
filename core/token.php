<?php
    
    /**
     * Manage tokens (definition, check, destroy)
     * 
     * @package Core
    **/
    class Token {
        
        const LIFETIME = 300; // 5 minutes
        
        
        /**
         * Generate a token
         * @param string    $name
        **/
        public static function generate($name) {
			Session::set("tokens.$name", array(
                'key' => $token = uniqid(sha1(time())),
                'time' => time()
            ));
			return $token;
        }
        
        
        /**
         * Check token validity
         * @param string    $name
         * @param string    $key
         * @param integer   $lifetime
        **/
        public static function authorized($name, $key, $lifetime = Token::LIFETIME) {
            $token = self::_get($name);
            if(!$token) return false;
            
            if(empty($lifetime))
                $lifetime = Token::LIFETIME;
            
            $elapsed = time() - $token['time'];
            
            return ($key == $token['key'] && $elapsed <= $lifetime) ? true : false;
        }
        
        
        /**
         * Destroy token
         * @param string    $name
        **/
        public static function destroy($name) {
            Session::delete("tokens.$name");
        }
        
        
        /**
         * Get token's value
         * @param string    $name
        **/
        private static function _get($name) {
            $tokens = Session::has('tokens') ? Session::get('tokens') : array();
                        
            if(!empty($tokens[$name]))
                return $tokens[$name];
            else
                return false;
        }
        
        
    }

?>