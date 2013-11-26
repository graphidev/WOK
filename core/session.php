<?php
    

    class Session {
        
        private static $secured = false;
        
        /**
         * Initialize session
        **/
        public static function start() {          
            
            // Start session
            session_start();
            
            // Is secured request
            self::$secured = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            
            // Language
            $accepted_languages = explode(',', SYSTEM_LANGUAGES);
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
            if(!self::isLogged()):
                self::set('id', !empty($id) ? id : uniqid());
                if($persistant)
                    Cookie::set('session', self::get('id'), SESSIONS_LIFETIME, true, true);
            endif;
        }
        
        /**
         * Check user session
        **/
        public static function isLogged() {
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
            if($isempty && !empty($_SESSION[$parameter])):
                return true;
            else:
                return isset($_SESSION[$parameter]);
            endif;
        }
        
        /**
         * Get session informations
        **/
        public static function get($parameter) {
            if(self::has($parameter))
                return $_SESSION[$parameter];
            else
                return false;
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
         * Set session information
        **/
        public static function set($parameter, $value) {
            $_SESSION[$parameter] = $value;
        }
        
        /**
         * Delete a session information 
        **/
        public static function delete($parameter) {
            unset($_SESSION[$parameter]);
        }
        
        
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