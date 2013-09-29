<?php

	class Session {
        
        public static $uniqid;
        public static $IP;
        public static $browser;
        public static $language;
        public static $HTTP_USER_AGENT;
        public static $HTTP_ACCEPT_LANGUAGE;
        public static $functions = array();
        
		public static function start() {
            
            session_start(); // Start sessions
            
            // Define session ID
            if(!isset($_SESSION['sess_'.SESSION_CRYPT])):
                self::set('uniqid', uniqid());
            endif;
            
            self::$uniqid = $_SESSION['sess_'.SESSION_CRYPT]['uniqid'];
            
            // Definse session language
            if(!empty($_SERVER["HTTP_CLIENT_IP"])):
				self::$IP = $_SERVER["HTTP_CLIENT_IP"];
			elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])):
				self::$IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
			else:
				self::$IP = $_SERVER["REMOTE_ADDR"];
			endif;
            
            // Define session language
            if(!self::get('language')):
            
                $accepted_languages = explode(',', SYSTEM_ACCEPT_LANGUAGES);
                if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])):
                    $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                    foreach($languages as $i => $language) {
                        $code = str_replace('-', '_', $language);
                        if(in_array($code, $accepted_languages))
                            self::$language = $code;
                    }
                endif;
    
                if(empty(self::$language))
                    self::$language = $accepted_languages[0];
                
                self::set('language', self::$language);
            
            else:
                self::$language = $_SESSION['sess_'.SESSION_CRYPT]['language'];
            endif;
        
            self::$browser =                @get_browser(null, true);
            self::$HTTP_USER_AGENT =        $_SERVER['HTTP_USER_AGENT'];
			self::$HTTP_ACCEPT_LANGUAGE =   $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			
        }
        
        public static function set($name, $value, $bruteforce = true) {
            $_SESSION['sess_'.SESSION_CRYPT][$name] = $value;
        }
        
        public static function get($name) {
            if(isset($_SESSION['sess_'.SESSION_CRYPT][$name]))
                return $_SESSION['sess_'.SESSION_CRYPT][$name];
            else
                return false;
        }
        
		public static function language($set = null) {
            if(!empty($set)):
                $accepted_languages = explode(',', SYSTEM_ACCEPT_LANGUAGES);
                if(in_array($set, $accepted_languages)):
                    self::$language = $set;
                    self::set('language', $set);
                endif;
            endif;
            return self::$language;
        }
        
        public static function browser($get = null) {
            if(!empty($get))
                return self::$browser[$get];
            else
                return self::$browser;
        }
		
		public static function token(){
			$token = uniqid(rand());
			$_SESSION['token_'.TOKEN_SALT][$token]['value'] = $token;
			$_SESSION['token_'.TOKEN_SALT][$token]['time'] = time();
			return $token;
		}

		public static function is_authorized_token($token, $duration = 5) {
            if(!empty($_SESSION['token_'.TOKEN_SALT][$token])):
                $maxtime = $_SESSION['token_'.TOKEN_SALT][$token]['time'] + $duration*60;
                if($token == $_SESSION['token_'.TOKEN_SALT][$token]['value'] && time() <= $maxtime ):
                    return true;
                else:
                    return false;
                endif;
            else:
                return false;
            endif;
		}
			
	}

?>