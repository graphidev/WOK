<?php
    
    class Token {
        
        /**
         * Generate a token
        **/
        public static function generate($name) {
            $token = uniqid(sha1(time()));
			$_SESSION['tokens'][$name] = array(
                'key' => $token,
                'time' => time()
            );
			return $token;
        }
        
        
        /**
         * Check token validity
        **/
        public static function authorized($name, $key, $lifetime) {
            $token = self::_get($name);
            if(!$token) return false;
            
            if(empty($lifetime))
                $lifetime = TOKENS_LIFETIME;
            
            $elapsed = time() - $token['time'];
            
            return ($key == $token['key'] && $elapsed <= $lifetime) ? true : false;
        }
        
        
        /**
         * Destroy token
        **/
        public static function destroy($name) {
            unset($_SESSION['tokens'][$name]);
        }
        
        /**
         * Get token
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