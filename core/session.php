<?php

	class Session {
        
        protected static $uniqid;
        protected static $ip;
        protected static $browser;
        protected static $language;
        protected static $HTTP_USER_AGENT;
        protected static $HTTP_ACCEPT_LANGUAGE;
                
        
		public function __construct() {

            // Define session ID
            if(!isset($_SESSION['sess_'.SESSION_CRYPT])):
                $this->set('uniqid', uniqid());
            endif;
            
            self::$uniqid = $_SESSION['sess_'.SESSION_CRYPT]['uniqid'];
            
            // Definse session language
            if(!empty($_SERVER["HTTP_CLIENT_IP"])):
				self::$ip = $_SERVER["HTTP_CLIENT_IP"];
			elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])):
				self::$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			else:
				self::$ip = $_SERVER["REMOTE_ADDR"];
			endif;
            
            // Define session language
            if(!$this->get('language')):
            
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
                
                $this->set('language', self::$language);
            
            else:
                self::$language = $_SESSION['sess_'.SESSION_CRYPT]['language'];
            endif;
        
            
            self::$browser =                @get_browser(null, true);
            self::$HTTP_USER_AGENT =        $_SERVER['HTTP_USER_AGENT'];
			self::$HTTP_ACCEPT_LANGUAGE =   $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			
        }
        
        public function set($name, $value, $bruteforce = true) {
            $_SESSION['sess_'.SESSION_CRYPT][$name] = $value;
        }
        
        public function get($name) {
            if(isset($_SESSION['sess_'.SESSION_CRYPT][$name]))
                return $_SESSION['sess_'.SESSION_CRYPT][$name];
            else
                return false;
        }
        
        public function ip() {
            return self::$ip;
        }
        
		public function language($set = null) {
            if(!empty($set)):
                $accepted_languages = explode(',', SYSTEM_ACCEPT_LANGUAGES);
                if(in_array($set, $accepted_languages)):
                    self::$language = $set;
                    $this->set('language', $set);
                endif;
            endif;
            return self::$language;
        }
        
        public function browser($get = null) {
            if(!empty($get))
                return self::$browser[$get];
            else
                return self::$browser;
        }
		
		public function set_token(){
			$token = uniqid(rand());
			$_SESSION['token_'.TOKEN_SALT]['value'] = $token;
			$_SESSION['token_'.TOKEN_SALT]['time'] = time();
			return $token;
		}

		public function is_token_authorized($token, $duration = 15) {
			$maxtime = $_SESSION['token_'.TOKEN_SALT]['time'] + $duration*60;
			if($token == $_SESSION['token_'.TOKEN_SALT]['value'] && time() <= $maxtime ):
				return true;
			else:
				return false;
			endif;
		}
			
	}

?>