<?php
    
    /**
     * cURL (class)
     *
     * @version 3.0.0
     * @author Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @licence CC BY 4.0 <http://creativecommons.org/licenses/by/4.0/>
     *
     * @package Libraries
    **/

    class cURL {
		
		const VERSION = '3.0.0';
        
        private $curl       = false;
        
        // Predefined request options
        private $options = array(
            CURLINFO_HEADER_OUT         => true,
            CURLOPT_RETURNTRANSFER      => true,
            CURLOPT_HEADER              => true,
			CURLOPT_VERBOSE             => true,
            CURLOPT_FOLLOWLOCATION      => true,
            CURLOPT_ENCODING            => '', 
            CURLOPT_USERAGENT           => '', 
            CURLOPT_AUTOREFERER         => true,
            CURLOPT_CONNECTTIMEOUT      => 120,
            CURLOPT_TIMEOUT             => 120, 
          	//CURLOPT_MAXREDIRS           => 10,
            CURLOPT_SSL_VERIFYHOST      => 2,
            CURLOPT_SSL_VERIFYPEER      => true,
           	CURLOPT_HTTPHEADER          => array(),
            CURLOPT_HTTPGET             => true,
            CURLOPT_POST                => false,
			CURLOPT_COOKIEJAR			=> '',
            CURLOPT_COOKIEFILE          => '',
		 	CURLOPT_COOKIESESSION       => true,
        );       
        
        /**
         * Init the cURL request
         * @param array     $options		Have a look to cURL::setOptions method
        **/
        public function __construct(array $options = array()) {
            $this->curl = curl_init();
            
            // Define default cURL agent
			if(!isset($options[CURLOPT_USERAGENT])) {
				$curl = curl_version();
				$this->options[CURLOPT_USERAGENT] =  'cURL/'.$curl['version'] . ' ('.php_uname('s').' '.php_uname('r').' '.php_uname('m').')' . ' PHP/'.PHP_VERSION;
			}
            
            // Overload options
			$this->setOptions($options);
        }
        
        
        /**
         * Define custom options or overload it
         * @param string     $options		Options as ($key => $value)
        **/
        public function setOptions(array $options) {
            $this->options = $this->options + $options;
        }
		
		
		/**
         * Define custom headers
         * @param string     $headers		Headers as ($name => $value)
        **/
		public function setHeaders(array $headers) {
			$this->options[CURLOPT_HTTPHEADER] = array_merge($this->options[CURLOPT_HTTPHEADER], $headers);
		}
		
		/**
		 * Define a user access
		 * @param string	$username
		 * @param string	$password
		**/
		public function setAuth($username, $password) {
			$this->setOptions(array(
				CURLOPT_USERPWD => $username.':'.$password,
				CURLOPT_HTTPAUTH => CURLAUTH_ANY
			));
		}
        
		
		/**
         * Run request (GET by default)
         * @param string     $url	Request url
         * @return mixed
        **/
        private function exec($url) {
            // Define request URL
            $this->options[CURLOPT_URL] = $url;
			
            // Run request
            curl_setopt_array($this->curl, $this->options);
            $response = curl_exec($this->curl);
            
            if(curl_errno($this->curl)) // Error while requesting
                throw new Exception(curl_error($this->curl), curl_errno($this->curl));

			
			$output = new StdClass;
			$data = explode("\r\n\r\n", $response, 2);
			
			if(!isset($data[1])) {
				$output->headers = array();
				$output->body = $data[0];
			}
			else {
				$output->headers = http_parse_headers($data[0]);
				$output->body = $data[1];	
			}
			
            return $output;
        }
        
        
        /**
         * Run a GET request
         * @param string    $url 	Request url
         * @param array     $data	Data to send
         * @return mixed
        **/
        public function get($url, $data = array()) {
			$this->options[CURLOPT_POST] = false;
            $this->options[CURLOPT_HTTPGET] = true;
            
			return $this->exec($url . http_build_query($data));
        }
        
        
        /**
         * Run a POST request
         * @param string    $url 	Request url
         * @param array     $data	Data to send
         * @return mixed
        **/
        public function post($url, $data = array()) {
            $this->options[CURLOPT_POST] = true;
            $this->options[CURLOPT_HTTPGET] = false;
            $this->options[CURLOPT_POSTFIELDS] = $data;
            
            return $this->exec($url);
        }
		
        /**
         * Run a PUT request
         * @param string    $url 	Request url
         * @param array     $data	Data to send
         * @return mixed
        **/
        public function put($url, $data = array()) {
			$this->options[CURLOPT_POST] = true;
            $this->options[CURLOPT_HTTPGET] = false;
			$this->options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $this->options[CURLOPT_POSTFIELDS] = http_build_query($data);
            
            return $this->exec($url);
        }
		
		 /**
         * Run a DELETE request
         * @param string    $url 	Request url
         * @return mixed
        **/
        public function delete($url) {
			$this->options[CURLOPT_POST] = true;
            $this->options[CURLOPT_HTTPGET] = false;
			$this->options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
			$this->options[CURLOPT_POSTFIELDS] = null;

            
            return $this->exec($url);
        }
		
		
		/**
		 * Allow to get cURL resource
		 * This method can be used to use native curl_getinfo function
		 * @param 
		**/ 
		public function getInstance() {
			return $this->curl;
		}
		
		/**
		 * Close cURL request
		**/
		public function  close() {
			curl_close($this->curl);	
		}
		
		public function __destroy() {
			$this->close();	
		}
		
    }
	

	/**
	 * Define alternative http_parse_headers function
	 * However, using pecl_http is a better way
	 * From  http://php.net/manual/en/function.http-parse-headers.php#112917
	**/
	if (!function_exists('http_parse_headers')) {
		function http_parse_headers ($raw_headers) {
			$headers = array(); // $headers = [];

			foreach (explode("\n", $raw_headers) as $i => $h) {
				$h = explode(':', $h, 2);

				if (isset($h[1])) {
					if(!isset($headers[$h[0]])) {
						$headers[$h[0]] = trim($h[1]);
					} else if(is_array($headers[$h[0]])) {
						$tmp = array_merge($headers[$h[0]],array(trim($h[1])));
						$headers[$h[0]] = $tmp;
					} else {
						$tmp = array_merge(array($headers[$h[0]]),array(trim($h[1])));
						$headers[$h[0]] = $tmp;
					}
				}
			}

			return $headers;
		}
	}

?>