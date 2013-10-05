<?php
    
    /**
     * cURL class
     * The class contains :
     * - Predefined parameters
     * - Exception errors
     * - Easier cURL integration
    **/

    class cURL {
        
        protected $interface;
        private $URI;
        private $response;
        private $infos;
        
        /**
         * Predefined options
        **/
        private $options = array(
            CURLINFO_HEADER_OUT         => true,
            CURLOPT_RETURNTRANSFER      => true,
            CURLOPT_HEADER              => false,
            CURLOPT_FOLLOWLOCATION      => true,
            CURLOPT_ENCODING            => '', 
            CURLOPT_USERAGENT           => '', 
            CURLOPT_AUTOREFERER         => true,
            CURLOPT_CONNECTTIMEOUT      => 120,
            CURLOPT_TIMEOUT             => 120, 
            CURLOPT_MAXREDIRS           => 10,
            CURLOPT_SSL_VERIFYHOST      => true,
            CURLOPT_SSL_VERIFYPEER      => true,
            CURLOPT_VERBOSE             => false,
            CURLOPT_COOKIESESSION       => true,
            
        );
        
        /**
         * cURL methods 
        **/
        const METHOD_GET        = 'GET';
        const METHOD_POST       = 'POST';
        
        /**
         * Init the cURL request
        **/
        public function __construct($url, $headers = array()) {
            $this->interface = curl_init($url);
            $this->URI = $url;
            
            if(!empty($headers))
                $this->options[CURLOPT_HTTPHEADER] = $headers;
        }
        
        /**
         * Define the cookies file
        **/
        public function cookies($path) {
            $this->options[CURLOPT_COOKIEFILE] = $path;
            $this->options[CURLOPT_COOKIEJAR] = $path;
        }
        
        /**
         * Add data to the request
         * To send file use '@/path/to/file'
        **/
        public function send($data, $method = cURL::METHOD_GET) {
            if($method == self::METHOD_POST):
                $this->options[CURLOPT_POST] = true;
                $this->options[CURLOPT_POSTFIELDS] = $data;
            
            elseif($method == self::METHOD_GET):
                $this->options[CURLOPT_URL] = $this->URI . http_build_query($data);
            
            endif;
        }
        
        /**
         * Define custom options or overload some predefined options
        **/
        public function options($options) {
            foreach($options as $key => $value) {
                $this->options[$key] = $value;
            }
        }
        
        /**
         * Send the cURL request
        **/
        public function exec() {
            if(empty($this->response)):
                if(empty($this->options[CURLOPT_USERAGENT])):
                    $curl = curl_version();
                    $this->options[CURLOPT_USERAGENT] = 'WOK/'.WOK_VERSION.' cURL/'.$curl['version'] . ' ('.php_uname('s').' '.php_uname('r').' '.php_uname('m').')' . ' PHP/'.PHP_VERSION;
                endif;
            
                curl_setopt_array($this->interface, $this->options);
                if(!curl_errno($this->interface)):
                    $this->response = curl_exec($this->interface);
                     $this->infos = curl_getinfo($this->interface);
                    
                else:
                    throw new Exception(curl_error($this->interface), curl_errno($this->interface));
                    
                endif;
                curl_close($this->interface);
            endif;
        }
        
        /**
         * Get cURL request infos
        **/
        public function info($name = 0) {
            $this->exec();
            
            if(!empty($name))
                return $this->infos[$name];
            else
                return $this->infos;
        }
        
        /**
         * Get cURL request body response
        **/
        public function response() {
            $this->exec();
            return $this->response;
        }
        
    }

?>