<?php
    

    class Session {
        
        private static $secured = false;
        
        
        /**
         * Initialize session
        **/
        public function __construct() {          
            
            // Start session
            session_start();
            
            // Is secured request
            self::$secured = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            
            // Language
            $accepted_languages = explode(' ', SYSTEM_LANGUAGES);
            if(self::has('language') && in_array(self::get('language'), $accepted_languages)):
                self::language(self::get('language'));
            
            elseif(Cookie::exists('language', true) && in_array(Cookie::get('language'), $accepted_languages)):
                self::language(Cookie::get('language'));
            
            elseif(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])):
                $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                foreach($languages as $i => $language) {
                    $code = str_replace('-', '_', $language);
                    if(in_array($code, $accepted_languages)):
                        self::language($code);
                        break;
                    else:
                        self::language(SYSTEM_DEFAULT_LANGUAGE);
                    endif;
                }
            endif;
                
            Cookie::set('language', self::get('language'));
            
            // Id
            if(!self::has('uniqid')):
                self::set('uniqid', Cookie::exists('uniqid') ? Cookie::get('uniqid') : uniqid(sha1(time())));
                Cookie::set('uniqid', self::get('uniqid'));
            endif;
            
        }
        
        /**
         * Set user as logged in
        **/
        public static function login($id = null, $persistant = true) {
            if(!self::exists()):
                self::set('id', !empty($id) ? id : uniqid());
                if($persistant)
                    Cookie::set('session', self::get('id'), SESSIONS_LIFETIME, true, true);
            endif;
        }
        
        
        /**
         * Check user session
        **/
        public static function exists() {
            if(self::has('id', true)):
                return true;
            else:
                if(Cookie::exists('session', true)):
                    self::set('id', Cookie::get('session', true));
                    return true;
                else:
                    return false;
                endif;
            endif;
        }
        
        
        /**
         * Log out user
        **/ 
        public static function logout() {
            Cookie::destroy('session');
            self::delete('id');
            session_unset();
            session_destroy();
        }
        
        
        /**
         * Check if session has parameter
        **/
        public static function has($parameter, $isempty = false) {
            $path = &$_SESSION;
            $nodes = explode('.', $parameter);
            foreach($nodes as $i => $node) {
                if(isset($path[$node]))
                    $path = &$path[$node]; 
                else
                    return false;
            }
            return $isempty ? !empty($path) : true;
        }
        
        /**
         * Get session informations
        **/
        public static function get($parameter, $default = false) {
            $path = &$_SESSION;
            $nodes = explode('.', $parameter);
            foreach($nodes as $i => $node) {
                if(isset($path[$node]))
                    $path = &$path[$node]; 
                else
                    return $default;
            }
            return $path;
        }
        
        /**
         * Set session information
        **/
        public static function set($parameter, $value) {
            $path = str_replace('.', "']['", $parameter);
            eval("\$_SESSION['$path']='$value';");
        }
        
        
        /**
         * Delete a session information 
        **/
        public static function delete($parameter) {
            unset($_SESSION[$parameter]);
        }
        
                /**
         * Get session id
        **/
        public static function id() {
            if(self::has('id', true)):
                return self::get('id'); 
            else:
                return self::get('uniqid');
            endif;
        }
        
        /**
         * Get or set session language
        **/
        public static function language($set = null) {
            if(!empty($set)):   
                self::set('language', $set);
                Cookie::set('language', $set);
            else:
                $language = self::get('language');
                if(!$language) $language = Cookie::get('language');
                
                return (!empty($language) ? $language : false);
            endif;
        }
        
    }

?>