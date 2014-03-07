<?php
    
    /**
     * Mail (class)
     *
     * @version 2.1
     * @author Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @licence CC BY 4.0 <http://creativecommons.org/licenses/by/4.0/>
    **/

    class cURL {
        
        private $curl       = false;
        private $output     = false;
        
        // Predefined request options
        private $options = array(
            CURLINFO_HEADER_OUT         => true,
            CURLOPT_RETURNTRANSFER      => true,
            CURLOPT_HEADER              => true,
            CURLOPT_FOLLOWLOCATION      => true,
            CURLOPT_ENCODING            => '', 
            CURLOPT_USERAGENT           => '', 
            CURLOPT_AUTOREFERER         => true,
            CURLOPT_CONNECTTIMEOUT      => 120,
            CURLOPT_TIMEOUT             => 120, 
            CURLOPT_MAXREDIRS           => 10,
            CURLOPT_SSL_VERIFYHOST      => 2,
            CURLOPT_SSL_VERIFYPEER      => true,
            CURLOPT_VERBOSE             => true,
            CURLOPT_COOKIESESSION       => true,
            CURLOPT_HTTPHEADER          => array(),
            CURLOPT_HTTPGET             => true,
            CURLOPT_POST                => false,
            CURLOPT_COOKIEFILE          => ''
        );
        
        
        // cURL request methods 
        const METHOD_GET        = 'GET';
        const METHOD_POST       = 'POST';
        
        
        /**
         * Init the cURL request
         *
         * @param array     $options
        **/
        public function __construct($options = array()) {
            $this->curl = curl_init();
            
            // Define default cURL agent
            $curl = curl_version();
            $this->setopt(CURLOPT_USERAGENT, 'cURL/'.$curl['version'] . ' ('.php_uname('s').' '.php_uname('r').' '.php_uname('m').')' . ' PHP/'.PHP_VERSION);
            
            // Redefine options
            foreach($options as $key => $value)
                $this->setopt($key, $value);
        }
        
        
        /**
         * Define custom options or overload some predefined options
         *
         * @param array     $options
        **/
        public function setopt($key, $value) {
            $this->options[$key] = $value;
        }
        
        
        /**
         * Define custom request headers
         *
         * @param array     $headers
        **/
        public function headers($headers) {
            $this->options[CURLOPT_HTTPHEADER] = array_merge($this->options[CURLOPT_HTTPHEADER], $headers);   
        }
        
        
        /**
         * Run a GET request
         *
         * @param string    $url
         * @param array     $data
         * @return mixed
        **/
        public function get($url, $data = array()) {
            $this->options[CURLOPT_HTTPGET] = true;
            $this->options[CURLOPT_URL] = $url . http_build_query($data);
            
            $this->exec($url);
        }
        
        
        /**
         * Run a POST request
         *
         * @param string    $url
         * @param array     $data
         * @return mixed
        **/
        public function post($url, $data = array()) {
            $this->options[CURLOPT_POST] = true;
            $this->options[CURLOPT_HTTPGET] = false;
            $this->options[CURLOPT_POSTFIELDS] = $data;
            
            $this->exec($url);
        }
        
        
        /**
         * Run request (GET by default)
         *
         * @param string    $url
         * @param array     $headers
         * @return mixed
        **/
        public function exec($url, $headers = array()) {  
            // Define request URL
            $this->options[CURLOPT_URL] = $url;
            $this->headers($headers);
            
            // Run request
            curl_setopt_array($this->curl, $this->options);
            $this->output = curl_exec($this->curl);
            
            if(curl_errno($this->curl)) // Error while requesting
                throw new Exception(curl_error($this->curl), curl_errno($this->curl));
                        
            return $this->output;
        }
        
        
        /**
         * Define the cookies file
         *
         * @param string    $path
        **/
        public function cookiesFile($path) {
            $this->options[CURLOPT_COOKIEFILE] = $path;
            $this->options[CURLOPT_COOKIEJAR] = $path;
        }
        
        
        /**
         * Return request response content
         *
         * @return mixed
        **/
        public function content() {
            if(!$this->output)
                throw new Exception('cUrl : Request must be executed before getting response');
            
            return $this->output;    
        }
        
        
        /**
         * Get a cURL request information
         *
         * @param string    $constant
         * @return mixed
        **/
        public function getinfo($constant = 0) {
            if(!empty($constant))
                return curl_getinfo($this->curl, $constant);
            
            else
                return curl_getinfo($this->curl);
        }
        
        
        /**
         * Close cURL session
        **/
        public function close() {
            curl_close($this->curl); 
        }
        
    }

?>