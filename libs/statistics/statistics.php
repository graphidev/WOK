<?php

	class statistics {
		private static $os = array(
			'Windows NT 6.2'       => 'Windows 8',
			'Windows NT 6.1'       => 'Windows Seven',
			'Windows NT 6.0'       => 'Windows Vista',
		    'Windows NT 5.2'       => 'Windows Server 2003',
		    'Windows NT 5.1'       => 'Windows XP',
		    'Windows NT 5.0'       => 'Windows 2000',
		    'Windows NT'           => 'Windows NT',
		    'Windows CE'           => 'Windows Mobile',
		    'Windows Phone'		   => 'Windows Phone',
		    'Win 9x 4.90'          => 'Windows Millenium.',
		    'Windows 98'           => 'Windows 98',
		    'Windows 95'           => 'Windows 95',
		    'Win95'                => 'Windows 95',
		    'Ubuntu'               => 'Linux Ubuntu',
		    'Fedora'               => 'Linux Fedora',
		    'Android'			   => 'Android',
		    'Linux'                => 'Linux',
		    'Unix'                 => 'Unix',
		    'iPod'				   => 'iOS',
		    'iPhone'               => 'iOS',
		    'iPad'				   => 'iOS',
		    'Macintosh'            => 'Mac OS X',
		    'Mac OS X'             => 'Mac OS X', 
		    'PLAYSTATION'		   => 'Linux PlayStation',
		    'Nintendo Wii'		   => 'Nintendo ES',
		    'SymbianOS'			   => 'Symbian'
		);
		private static $devices = array(
			'iPod'					=> 'iPod',
			'iPad'      		 	=> 'iPad',
			'iPhone'    		 	=> 'iPhone',
			'blackberry'         	=> 'BlackBerry',
			'Nexus 4'				=> 'Nexus 4',
			'Nexus 7'				=> 'Nexus 7',
			'Nexus 10'				=> 'Nexus 10',
			'android'       	 	=> 'Mobile/Tablet',
			'Mac_PowerPC'		 	=> 'Apple Mac Power PC',
			'Macintosh; Intel'		=> 'Apple Mac Intel',
			'PLAYSTATION'			=> 'PlayStation',
			'FbxQmlTV'				=> 'FreeBox TV',
			'Nintendo Wii'			=> 'Nintendo Wii',
			'Kindle'				=> 'Amazon Kindle',
			'Windows NT 6.2'        => 'PC/tablet',
			'Windows NT 6.1'        => 'PC (supposed)',
			'Windows NT 6.0'        => 'PC (supposed)',
			'Windows NT 5.2'        => 'Serveur Windows',
			'Windows NT 5.1'        => 'PC (supposed)',
			'Windows NT 5.0'        => 'PC (supposed)',
			'Windows NT'            => 'PC (supposed)',
			'Windows CE'            => 'Windows Phone',
			'Windows Phone'			=> 'Windows Phone',
			'Win 9x 4.90'           => 'PC (supposed)',
			'Windows 98'            => 'PC (supposed)',
			'Windows 95'            => 'PC (supposed)',
			'Win95'                 => 'PC (supposed)',
			'Ubuntu'                => 'PC (supposed)',
			'Fedora'                => 'PC (supposed)',
			'Linux'                 => 'PC/Serveur Linux (supposed)',
			'Unix'                  => 'PC/Serveur UNIX (supposed)',
			'SymbianOS'				=> 'Nokia Mobile'
		);
		private static $browsers = array(
			'Kindle'				=> 'Kindle',
			'Opera Mobi'			=> 'Opera Mobile',
			'Opera Mini'			=> 'Opera Mini',
			'Opera' 				=> 'Opera',
			'Fennec'				=> 'Fennec (Firefox mobile)',
			'Firefox' 				=> 'Mozilla Firefox',
			'Chrome'				=> 'Google Chrome',
			'Maxthon'				=> 'Maxthon',
			'Safari'				=> 'Safari',
			'MSIE'					=> 'Internet Explorer',
			'Trident/'				=> 'Internet Explorer',
			'Netscape/'				=> 'Netscape',
			'Minimo/'				=> 'Minimo'
		);
		
		private static $mobiles = array(
			'iPod', 'iPad', 'iPhone', 'iOS',
			'blackberry', 'SymbianOS', 'Android',
			'Kindle', 'Windows Phone'
		);
		
		private static $bots = array(
			'Googlebot/'				=> 'GoogleBot',
			'Feedfetcher-Google'		=> 'GoogleBot',
			'Googlebot-Mobile/'			=> 'GoogleBot Mobile',
			'Googlebot-Image/'			=> 'GoogleBot Image',
			'Google Web Preview'		=> 'Google Preview',
			'Google-Site-Verification'	=> 'Google Webmaster Tools (Site verification)',	
			'Google-Sitemaps/'			=> 'Google Webmaster Tools (Sitemap)',
			'msnbot/'					=> 'MSN Bot',
			'TweetmemeBot/'				=> 'Tweetmeme',
			'facebookexternalhit/'		=> 'Facebook Bot',
			'UnwindFetchor/'			=> 'GNIP Social Bot',
			'LinkedInBot'				=> 'LinkedIn Bot',
			'PaperLiBot/'				=> 'PaperLi Bot',
			'bingbot/'					=> 'Bing Bot',
			'WBSearchBot/'				=> 'Ware Bay Bot',
			'AhrefsBot/'				=> 'Ahrefs Bot',
			'Twikle/'					=> 'Twikle Bot',
			'Twitterbot/1'				=> 'Twitter Bot',
			'JS-Kit'					=> 'JS-Kit',
			'LinksCrawler'				=> 'LinksCrawler',
			'W3C_Validator/'			=> 'W3C Validator',
			'Validator.nu/'				=> 'W3C Validator',
			'W3C_CSS_Validator_JFouffa/'=> 'W3C CSS Validator',
			'Wget/'						=> 'Wget (public)',
			'Exabot/'					=> 'Exabot (Exalead)',
			'NG/'						=> 'Exabot-NG (Exalead)',
			'AcoonBot/'					=> 'Acoon',
			'heritrix/'					=> 'Heritrix',
			'SurveyBot/'				=> 'SurveyBot (DomainTools)',
			'Yahoo! Slurp'				=> 'Yahoo Search',
			'bnf.fr_bot;'				=> 'Bibliothèque Nationale de France (Heritrix)',
			'Apple-PubSub/'				=> 'RSS-reader Safari',
			'FlipboardProxy/'			=> 'Flipboard Proxy',
			'AskTbORJ/'					=> 'Ask Toolbar',
			'AlexaToolbar/'				=> 'Alexa Toolbar / Botcrawl',
			'ia_archiver'				=> 'ia_Archiver (Alexa)',
			//'via translate.google.com'	=> 'Google Translate', // À vérifier
			
			// Bots considérés comme problématiques
			'NetcraftSurveyAgent/'		=> 'Netcraft Survey Bot',
			'YandexBot/'				=> 'YandexBot', // Nombreuses requêtes (Russe)
			'YandexImages/'				=> 'YandexBot Images', // Idem
			'Butterfly/'				=> 'Butterfly (pirate)',
			'SISTRIX Crawler'			=> 'SISTRIX Bot', // 
			'DoCoMo/'					=> 'DoCoMo', // bot chinois spammeur
			'Ezooms/'					=> 'Ezooms Bot', // Nombreuses requêtes (US)
			'VoilaBot'					=> 'VoilaBot', // Aspirateur (France)
			'TurnitinBot/'				=> 'Turnitin Bot', // Dit anti-plagiat chez les étudiants
			'Jigsaw/'					=> 'Jigsaw Bot', // 
			'Java/'						=> 'JAVA bot (unknow)'
		);
		
		private $HTTP_USER_AGENT;
		private $HTTP_ACCEPT_LANGUAGE;
		private $GET_BROWSER;
		private static $visitor = array();
		private $is_bot = false;
		private $is_mobile = false;
		private $logs = array();
		private $path;
		
		public function __construct($path) {
			$this->HTTP_USER_AGENT =  $_SERVER['HTTP_USER_AGENT'];
			$this->HTTP_ACCEPT_LANGUAGE =  $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			$this->GET_BROWSER = @get_browser(null, true);
			$this->path = root($path);
		}
		
		function analyze($referer = null) {
			/**
				Visitor default informations
			*/
			$session_id = session_id();
			setcookie('analytics', $session_id, time()+3600*24*183);
			
			self::$visitor['date'] = date('Y-m-d H:i:s');
			self::$visitor['cookie'] = (!empty($_COOKIE['analytics']) ? $_COOKIE['analytics'] : $session_id);
			self::$visitor['access'] = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $referer);
			
			/**
				Visitor IP
			*/
			if(!empty($_SERVER["HTTP_CLIENT_IP"])):
				self::$visitor['IP'] = $_SERVER["HTTP_CLIENT_IP"];
				
			elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])):
				self::$visitor['IP'] = $_SERVER["HTTP_X_FORWARDED_FOR"];
			else:
				self::$visitor['IP'] = $_SERVER["REMOTE_ADDR"];
			endif;
			
			/**
				Visitor browser
			*/
			self::$visitor['browser'] = '';
			$current = strtolower($this->HTTP_USER_AGENT); 
			foreach(self::$browsers as $k => $v ) {
				if(ereg( strtolower( $k ), $current)):
					self::$visitor['browser'] = $v;
					break;
				endif;
			}
			
			/**
				Visitor device
			*/
			self::$visitor['device'] = '';
			$current = strtolower($this->HTTP_USER_AGENT); 
			foreach(self::$devices as $k => $v ) {
				if(ereg( strtolower( $k ), $current)):
					self::$visitor['device'] = $v;
					break;
				endif;
			}
			
			/**
				Visitor device OS
			*/
			self::$visitor['OS'] = '';
			$current = strtolower($this->HTTP_USER_AGENT); 
			foreach(self::$os as $k => $v ) {
				if(ereg(strtolower( $k ), $current)):
					self::$visitor['OS'] = $v;
					break;
				endif;
			}
			
			/**
				Visitor location
			*/
			$ip = explode('.', str_replace('::1', '127.0.0.1', self::$visitor['IP']));
			list($a,$b,$c,$d) = $ip;
			$calc = (16777216*$a + 65536*$b + 256*$c + $d);
			
			$handle = fopen($this->path.'/geoip.csv', "r");
			while(($data = fgetcsv($handle, 1000, ",")) !== FALSE):
				if(($calc >= $data[2]) && ($calc <= $data[3])):
					break;
				endif;
			endwhile;
			fclose($handle);
			
			if(isset($data[4]) && isset($data[5])):
				self::$visitor['location'] = '['.$data[4].'] '.$data[5];
				self::$visitor['country_code'] = $data[4];
			else:
				self::$visitor['location'] = '';
				self::$visitor['country_code'] = '';
			endif;
			
			/**
				Visitor language
			*/
			self::$visitor['language'] = (isset($_COOKIE['language']) ? $_COOKIE['language'] : $this->HTTP_ACCEPT_LANGUAGE);
			
			$language = (isset($_COOKIE['language']) ? $_COOKIE['language'] : $this->HTTP_ACCEPT_LANGUAGE);
			
			$cut = strpos(strtolower($language), ',');
			if($cut != false && $cut <3) $language = substr($language, $cut+1);
			$cut = strpos(strtolower($language), ';');
			if(!$cut || $cut > 5) $cut = strpos(strtolower($language), ',');
			
			if($cut):
				self::$visitor['language'] = substr($language, 0, $cut);
			endif;
		}

		public function is_bot() {	
			if(empty($this->HTTP_USER_AGENT)):
				self::$visitor['botname'] = 'Empty USER-AGENT (Bot supposed)';
				$this->is_bot = true;
			else:
				$current = strtolower($this->HTTP_USER_AGENT); 
				foreach(self::$bots as $k => $v ) {
					if(ereg( strtolower( $k ), $current)):
						self::$visitor['botname'] = $v;
						$this->is_bot = true;
						break;
					endif;
				}
			endif;
			
			return $this->is_bot;
		}
		
		public function is_mobile() {
			if(!empty($_COOKIE['mobile']) && $_COOKIE['mobile'] == 'true'):
				$this->is_mobile = true;
			elseif(in_array(self::$visitor['OS'], self::$mobiles) || 
			in_array(self::$visitor['device'], self::$mobiles) || 
			in_array(self::$visitor['browser'], self::$mobiles)):
				$this->is_mobile = true;
			else:
				$this->is_mobile = false;
			endif;
			
			return $this->is_mobile;
		}
		
		public function register($referer = null) {
			
			$this->analyze($referer);
			
			$this->path .= date('/Y/m/d');
			if(!is_dir($this->path)):
				mkdir($this->path, 0755, true);
			endif;
			
			// Regiter visit
			if($this->is_bot()):
			
				$filename = 'bots.xml';
				
				// Open xml file
				if(file_exists($this->path."/$filename")):
					$dom = new DOMDocument();
					$dom->load($this->path."/$filename");
					$root = $dom->getElementsByTagName('visits')->item(0);
				else:
					$dom = new DOMDocument('1.0', 'UTF-8');
					$list = $dom->createElement('visits');
					$dom->appendChild($list);
					$root = $dom->getElementsByTagName('visits')->item(0);
				endif;
				
				// Ajout d'un cas
				$case = $dom->createElement("case");
				$new = $root->appendChild($case);
				$case->setAttribute("id", uniqid());
				
				// Ajout des valeurs
				$date = $dom->createElement("date", date('Y-m-d H:i:s'));
				$url = $dom->createElement("url", strip_host_root($_SERVER['REQUEST_URI']));
				$ip = $dom->createElement("IP", self::$visitor['IP']);
				$name = $dom->createElement("name", self::$visitor['botname']);
				$referer = $dom->createElement("referer", self::$visitor['access']);
				$user_agent = $dom->createElement("user_agent", $this->HTTP_USER_AGENT);
				$accept_language = $dom->createElement("accept_language", $this->HTTP_ACCEPT_LANGUAGE);
				
				
				// Enregistrement des valeurs dans le cas
				$new->appendChild($date);
				$new->appendChild($url);
				$new->appendChild($ip);
				$new->appendChild($name);
				$new->appendChild($referer);
				$new->appendChild($user_agent);
				$new->appendChild($accept_language);
				
				// save
				$dom->save($this->path."/$filename");
				
			else:
			
				$filename = date('H').'.xml';
				
				// Open xml file
				if(file_exists($this->path."/$filename")):
					$dom = new DOMDocument();
					$dom->load($this->path."/$filename");
					$root = $dom->getElementsByTagName('visits')->item(0);
				else:
					$dom = new DOMDocument('1.0', 'UTF-8');
					$list = $dom->createElement('visits');
					$dom->appendChild($list);
					$root = $dom->getElementsByTagName('visits')->item(0);
				endif;
				
				// Ajout d'un cas
				$case = $dom->createElement("case");
				$new = $root->appendChild($case);
				$case->setAttribute("id", uniqid());
				
				// Ajout des valeurs
				$date = $dom->createElement("date", date('Y-m-d H:i:s'));
				$url = $dom->createElement("url", strip_host_root($_SERVER['REQUEST_URI']));
				$ip = $dom->createElement("IP", self::$visitor['IP']);
				$browser = $dom->createElement("browser", self::$visitor['browser']);
				$browser->setAttribute("mobile", ($this->is_mobile() ? true : 0));
				$browser->setAttribute("version", (!empty($this->GET_BROWSER['version']) ? $this->GET_BROWSER['version'] : null));				
				$device = $dom->createElement("device", self::$visitor['device']);
				$os = $dom->createElement("OS", self::$visitor['OS']);
				$language = $dom->createElement("language", self::$visitor['language']);
				$location = $dom->createElement("location", self::$visitor['location']);
				$country = $dom->createElement("country", self::$visitor['country_code']);
				$referer = $dom->createElement("referer", self::$visitor['access']);
				$session = $dom->createElement("session", self::$visitor['cookie']);
				$user_agent = $dom->createElement("user_agent", $this->HTTP_USER_AGENT);
				$accept_language = $dom->createElement("accept_language", $this->HTTP_ACCEPT_LANGUAGE);
				
				
				// Enregistrement des valeurs dans le cas
				$new->appendChild($date);
				$new->appendChild($url);
				$new->appendChild($ip);
				$new->appendChild($browser);
				$new->appendChild($device);
				$new->appendChild($os);
				$new->appendChild($language);
				$new->appendChild($location);
				$new->appendChild($country);
				$new->appendChild($referer);
				$new->appendChild($session);
				$new->appendChild($user_agent);
				$new->appendChild($accept_language);
				
				// save
				$dom->save($this->path."/$filename");
				
			endif;	
		}
		
		public function read($from, $upto, $bots = false) {
			$year = substr($from, 0, 4);
			$month = substr($from, -5, -3);
			$day = substr($from, -2, 2);
			
			$this->logs = array();
						
			while("$year$month$day" <= str_replace('-', '', $upto)):
				
				if(is_dir(root($this->path."/$year/$month/$day"))):
					
					if($bots):
						if(file_exists(root($this->path."/$year/$month/$day/bots.xml"))):
							$dom = new DomDocument();
							$dom->load(root($this->path."/$year/$month/$day/bots.xml"));
							$node = $dom->documentElement;
							$list = $dom->getElementsByTagName("case");
							
							foreach($list as $i => $case) {
							 	$this->logs[] = array(
							 		'date' => $case->getElementsByTagName('date')->item(0)->nodeValue,
								 	'url' => $case->getElementsByTagName('url')->item(0)->nodeValue,
								 	'IP' => $case->getElementsByTagName('IP')->item(0)->nodeValue,
								 	'name' => $case->getElementsByTagName('name')->item(0)->nodeValue
								 );
							}
						endif;
						
					else:

						for($hour=0; $hour<=23; $hour++) {							
							$hour = add0($hour);
			
							if(file_exists(root(this->path."/$year/$month/$day/$hour.xml"))):

								$dom = new DomDocument();
								$dom->load(root($this->path."/$year/$month/$day/$hour.xml"));
								$node = $dom->documentElement;
									
								$list = $dom->getElementsByTagName("case");
								
								foreach($list as $i => $case) {
									
									if($case->getElementsByTagName('browser')->item(0)->hasAttribute('version') 
                                       && $case->getElementsByTagName('browser')->item(0)->getAttribute('version') != ''):
										$version = $case->getElementsByTagName('browser')->item(0)->getAttribute('version');
									else:
										$browser = @get_browser($case->getElementsByTagName('user_agent')->item(0)->nodeValue, true);
										$version = $browser['version'];
									endif;
									
									$this->logs[] = array(
										'id' => $case->getAttribute('id'),
									 	'date' => $case->getElementsByTagName('date')->item(0)->nodeValue,
									 	'url' => $case->getElementsByTagName('url')->item(0)->nodeValue,
									 	'IP' => $case->getElementsByTagName('IP')->item(0)->nodeValue,
									 	'session' => $case->getElementsByTagName('session')->item(0)->nodeValue,
								 		'browser' => $case->getElementsByTagName('browser')->item(0)->nodeValue,
								 		'is_mobile' => $case->getElementsByTagName('browser')->item(0)->getAttribute('mobile'),
								 		'version' => $version,
								 		'OS' => $case->getElementsByTagName('OS')->item(0)->nodeValue,
								 		'device' => $case->getElementsByTagName('device')->item(0)->nodeValue,
								 		'referer' => $case->getElementsByTagName('referer')->item(0)->nodeValue,
										'language' => $case->getElementsByTagName('language')->item(0)->nodeValue,
								 		'location' => $case->getElementsByTagName('location')->item(0)->nodeValue,
								 		'country' => $case->getElementsByTagName('country')->item(0)->nodeValue
									);
								}
								
							endif;
							
						}
						
					endif;
					
				endif;
				
				if($day == date('t')):  
					$day = '01';
					if($month == 12):
						$month = '01';
						$year++;
					else:
						$month = add0($month+1);
					endif;
				else:
					$day = add0($day+1);
				endif; 
				
			endwhile;
						
		}

		public function total($object = null, $value = null) {
			$count = 0;
			foreach($this->logs as $index => $value) {
				if(isset($this->logs[$index]['IP'])):
					$count++;
				endif;
			}
			return $count;
		}
		
		public function get_logs($order_by = null) {
			if(empty($order_by)):
				foreach($this->logs as $index => $log){
					if(!is_array($log) || count($log) == 1):
						unset($this->logs[$index]);
					endif;
				}
				return array_reverse($this->logs);
			endif;
		
			$array = array();
			foreach($this->logs as $i => $visit) {
			
				if($order_by == 'date'): // tri par date
					$date = substr($visit[$order_by], 0, -9);

					if(isset($array[$date])):
						$array[$date]++;
					else:
						$array[$date] = 1;
					endif;
				
				elseif($order_by == 'people/date'): // tri par visiteurs uniques/date
					$date = substr($visit['date'], 0, -9);
				
					if(isset($array[$date])):
						if(isset($array[$date][$visit['session']])):
							$array[$date][$visit['session']]++;
						else:
							$array[$date][$visit['session']] = 1;
						endif;
					else:
						$array[$date][$visit['session']] = 1;
					endif;
					
				elseif($order_by == 'people'):
					if(isset($array[$visit['session']])):
						$array[$visit['session']]++;
					else:
						$array[$visit['session']] = 1;
					endif;
				
				elseif($order_by == 'browser/version'): // Tri par langue
					if(!empty($array[$visit['browser'].' '.$visit['version']])):
						$array[$visit['browser'].' '.$visit['version']]++;
					else:
						$array[$visit['browser'].' '.$visit['version']] = 1;
					endif;
				
				elseif($order_by == 'language'): // Tri par langue
					if(empty($visit[$order_by])):
						$language = '';
					else:
						$language = strtolower(substr($visit[$order_by], 0,2));
					endif;
						
					if(isset($array[$language])):
						$array[$language]++;
					else:
						$array[$language] = 1;
					endif;
					
				
				else: // Default fields order
					if(isset($this->logs[$i]['IP'])):
						if(isset($array[$visit[$order_by]])):
							$array[$visit[$order_by]]++;
						else:
							$array[$visit[$order_by]] = 1;
						endif;
					endif;
				endif;
			}
			
			if($order_by == 'people/date'):
				foreach($array as $date => $visitors) {
					$array[$date] = count($visitors);
				}
			endif;
			
			return $array;
		}
				
		public function unique_visitors() {			
			$x = $this->total_visits();
			$r = null; // Visiteurs avec cookie mais IP != (%)
			$p = null; // Visiteurs sans cookies(%)
			
			$visitors = array();
			$visits = array();
			foreach($this->logs as $i => $visit) {
				if(count($this->logs[$i]) > 1):
					if(!empty($visit['cookie']) && $visit['cookie'] != 0):
						list($cip, $csid) = explode('&', $visit['cookie']);
						if($cip != $visit['IP'] && $csid != 'session_id'):
							$r++;
						endif;
					else:
						$p++;
					endif;
					
					$IDs = array($visit['IP'], $visit['browser'], $visit['OS'], $visit['user_agent']);
					if(!in_array($IDs, $visitors)):
						$visitors[] = $IDs;
						$key = array_search($IDs, $visitors);
						$visits[$key] = 1; 
					else:
						$key = array_search($IDs, $visitors);
						$visits[$key] = 1; 
					endif;
					//unset($IDs['date'], $IDs['language'],$IDs['url'], $IDs['access'], $IDs['cookie']);
					//$IDs = implode('|', $visit);
				endif;
			}
			
			
			$v = count($visitors);
			
			/*
			$r = ($r/$v)*100;
			$p = ($p/$v)*100;
			$n = array_sum($visits)/$x;
			
			$algo = $x-($n-1)*$p*$r*$x;
			*/
			
			return $v;
			
		}
	
	}

?>