<?php
    

    class Session {         
                
        
        /**
         * Run a session
        **/
        public function __construct() {
            session_start();   
        }
        
        /**
         * Log out user
        **/ 
        public static function clean($persistent = true) {                        
            if(ini_get('session.use_cookies')):
                $cookie = session_get_cookie_params();
                setCookie(session_name(), '', 1, $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
            endif;
            
            if($persistent): // Remove persistent session            
                $cookies = Cookie::all();
                foreach($cookies as $name) {
                    if(substr($name, 8) == 'session_')
                        Cookie::destroy($name);
                }
            endif;
            
            $_SESSION = array();
            @session_destroy();
        }
        
        
        /**
         * Check if session has parameter
         *
         * @param string    $parameter
         * @param boolean   $strict
        **/
        public static function has($parameter, $strict = false) {
            $path = &$_SESSION;
            $nodes = explode('.', $parameter);
            foreach($nodes as $i => $node) {
                if(isset($path[$node]))
                    $path = &$path[$node]; 
                else
                    return false;
            }
            return $strict ? !empty($path) : true;
        }
        
        /**
         * Get session informations
         *
         * @param string    $parameter
         * @param mixed     $default
         * @return mixed
        **/
        public static function get($parameter, $default = false) {
            return array_value($parameter, $_SESSION, $default);
        }
        
        /**
         * Set session information
         *
         * @param string    $parameter
         * @param mixed     $value
        **/
        public static function set($parameter, $value, $persistent = false) {
            array_set($parameter, $value, $_SESSION);
            
            if($persistent): // Save session's value as crypted cookie
                if(is_array($value) || is_object($value))
                    $value = serialize($value);
            
                Cookie::set(str_replace('.', '_', "session.$parameter"), $value, ini_get('session.gc_maxlifetime'), true);
            
            endif;
        }
        
        
        /**
         * Delete a session information
         *
         * @param string    $parameter
         * @return mixed
        **/
        public static function delete($parameter) {
            return array_unset($parameter, $_SESSION);
        }
        
        
    }

?>