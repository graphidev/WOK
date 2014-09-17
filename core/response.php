<?php
    
    /**
     * Define Response headers and body.
     * It also manage view file caching and
     * routers caching for every response type
     *
     * @package Core
    **/
    class Response {
        
        private $headers     = array();
        private $content     = null;
        private $code        = 200;
        private $data        = array();
        private $handler     = null;
        private $cachetime   = null;
        private $cachefile   = null;
        
        /**
         * Cache constants
        **/
        const CACHE_PUBLIC          = 'PUBLIC'; // Cache public
        const CACHE_PROTECTED       = 'PROTECTED'; // Cache for autheticaed only
        const CACHE_PRIVATE         = 'PRIVATE'; // Cache private
        const DISABLE_CACHE         = 0; // Do not cache
        const CACHETIME_SHORT       = 360; // 1 minutes
        const CACHETIME_MEDIUM      = 216000; // 1 hour
        const CACHETIME_LONG        = 5184000; // 24 hours
        
        /**
         * Generate a new response object
         * This constructor function must not 
         * be overloaded and stay private
        **/
        private function __construct(){}
        
        /**
         * Define headers.
         * Custom headers must begin with X-
         * @param array     $headers
        **/
        public function headers($headers) {
            $this->headers = array_merge($this->headers, $headers);
            return $this;
        }
                
        /**
         * Define response header status (with HTTP code)
         * @param integer   $code
         * @param string    $type
        **/
        public function status($code, $type = null) {
            $this->code = $code;
                
            if(!empty($type))  
                $this->headers(array('Content-Type' => $type));
        }
        
        /**
         * Define data to use for response. 
         * A closure function as parameter will be executed 
         * provided that cached file does not exists.
         * @param mixed   $data
        **/
        public function assign($data) {
            $this->data = $data;
            return $this;
        }
        
        
        /**
         * Define response content handler
         * @param closure   $function
        **/
        public function handler(Closure $function) {        
            $this->handler = $function;
            return $this;
        }        
        
        
        /**
         * Redirect permanently or temporarily
         *
         * @param string    $target
         * @param boolean   $permanent
        **/
        public static function redirect($target, $permanent = false) {   
            $response = new Response;
            $response->code = ($permanent ? 301 : 302);
            $response->headers(array('Location'=> $target));
            return $response;
        }
        
        
        /**
         * Generate response cache
         * Use headers and file
         * @param integer   $time
         * @param string    $status
         * @param mixed     $file
        **/
        public function cache($time = self::CACHETIME_SHORT, $status = self::CACHE_PROTECTED, $file = false) {
            // Private cache : do not cache
            if(!$time || $status == self::CACHE_PRIVATE || SYSTEM_DEBUG):
                $headers = array(
                    'Cache-Control'  => 'private, no-cache, no-store, must-revalidate, proxy-revalidate',
                    'Pragma'         => 'no-cache'
                );
            
            // Public
            elseif($time):
                $headers['Cache-Control'] =  "max-age=$time, s-maxage=$time";
            
                if($status == self::CACHE_PROTECTED): // Public but do not cache
                    $headers['Cache-Control'] .= ', public, no-cache, must-revalidate';
                    $headers['Pragma'] = 'no-cache';
                    
                else: // Public : cache if possible
                    $headers['Cache-Control'] .= ', public';
                    $headers['Pragma'] = 'cache';

                endif;
                
                $date = new DateTime(date('r', time()+$time));
                $date->setTimezone(new DateTimeZone('GMT'));
                $headers['Expires'] = $date->format('r');
            endif;
            
            // Send headers
            $headers['Cache-Control'] .= ', no-transform'; // Never transform outputed data
            $headers['Vary'] = 'Accept-Encoding';
            
            $this->headers($headers); 
            
            if($file && !SYSTEM_DEBUG): // Cache file
                $this->cachetime = $time;
                        
                if($status == self::CACHE_PROTECTED)
                    $this->cachefile = $file.'-'.session_id();
                else
                    $this->cachefile = $file;
            endif;
                                    
            return $this;
        }        
        
        /**
         * Define a view response
         * @param string    $template
         * @param integer   $status
        **/
        public static function view($template, $status = 200) {
            $response = new Response;
            $response->status($status, 'text/html; charset=utf8');
            $response->content = function() use($response, $template) {
                
                // Update cache file path
                $response->cachefile .= '.html'; 
                       
                // Output cached view
                if(!empty($response->cachetime) && Cache::exists($response->cachefile, $response->cachetime) 
                   && Cache::time($response->cachefile) > filemtime(root(PATH_TEMPLATES."/$template.php"))):
                
                    Cache::get($response->cachefile);

                else: // Generate view
                    
                    // Execute data's requests
                    if(is_closure($response->data))
                        $response->data = call_user_func($response->data);

                    $buffer = View::parse($template, $response->data);       
                
                    if(!is_null($response->handler))
                        $buffer = call_user_func($response->handler, $buffer, $response->data, $response->code);

                    if(!empty($response->cachetime))
                        Cache::register($response->cachefile, $buffer);

                    echo $buffer;

                endif; 
            
            };
            
            return $response;
        }
        
        /**
         * Define an HMTL response
         * @param array     $content    The HTML content
         * @param integer   $status     The response code
        **/
        public static function html($content = null, $status = 200) {
            $response = new Response;
            $response->status($status, 'text/html; charset=utf8');
            $response->content = function() use($response, $content) {
                
                // Update cache file path
                $response->cachefile .= '.html';
                
                if(!empty($response->cachetime) && Cache::exists($response->cachefile, $response->cachetime)) {
                    
                     Cache::get($response->cachefile);
                    
                }
                else {
                
                    // Execute data's requests
                    if(is_closure($content))
                        $content = call_user_func($content);
                    
                    if(!empty($content))
                        $response->data = $content;
                                            
                    if(!empty($response->cachetime))
                        Cache::register($response->cachefile, $response->data);
                    
                    echo $response->data;
                
                }
            
            };
            
            return $response;
        }
        
        /**
         * Define JSON response
         * @param array     $data
         * @param integer   $status
        **/
        public static function json(array $data, $status = 200) {
            $response = new Response;
            $response->status($status, 'application/json; charset=utf-8');
            $response->content = function() use($response, $data) {
                
                
                if(!empty($response->cachetime) && Cache::exists($response->cachefile, $response->cachetime)) {
                    
                     Cache::get($response->cachefile);
                    
                }
                else {
                
                    // Execute data's requests
                    if(is_closure($response->data))
                        $this->data = call_user_func($response->data);
                    
                    if(!empty($data))
                        $response->data = array_merge($response->data, $data);
                    
                    $json = json_encode($response->data);
                    
                    if(!empty($response->cachetime))
                        Cache::register($response->cachefile, $json);
                    
                    echo $json;
                
                }
  
            };
            return $response;
        }
        
        
        /**
         * Define XML response
         * @param array     $array
         * @param integer   $status
        **/
        public static function xml(array $data = null, $status = 200) {
            $response = new Response;
            $response->status($status, 'application/xml; charset=utf-8');
            $response->content = function() use($response, $data) {
                
                // Update cache file path
                $response->cachefile .= '.xml';
                
                if(!empty($response->cachetime) && Cache::exists($response->cachefile, $response->cachetime)) {
                    
                     Cache::get($response->cachefile);
                    
                }
                else {
                
                    // Execute data's requests
                    if(is_closure($response->data))
                        $response->data = call_user_func($response->data);
                    
                    if(!empty($data))
                        $response->data = array_merge($response->data, $data);
                    
                    $xml = xml_encode($response->data, 'document');
                    
                    if(!empty($response->cachetime))
                        Cache::register($response->cachefile, $xml);
                    
                    echo $xml;
                
                }
  
            };
            return $response;
        }
        
        
        /**
         * Define text response
         * @param string    $string
         * @param integer   $status
        **/
        public static function text($string, $status = 200) {
            $response = new Response;
            $response->status($status, 'text/plain; charset=utf-8');
            $response->content = function() use($response, $string) {
                
                // Update cache file path
                $response->cachefile .= '.txt';
                                
                if(!empty($response->cachetime) && Cache::exists($response->cachefile, $response->cachetime)) {
                    
                    Cache::get($response->cachefile);
                    
                }
                else {
                
                    if(!empty($string))
                        $response->data = $string;
                    
                    if(is_closure($response->data))
                        $response->data = call_user_func($response->data);

                    if(!empty($response->cachetime))
                        Cache::register($response->cachefile, $response->data);
                    
                    echo $response->data;
                
                }
                 
            };
            
           
            
            return $response;
        }
        
        
        /**
         * Define file response (also can force download)
         * @param string    $path
         * @param boolean   $download
         * @param integer   $status
         *
         * @note Force Content-Type header with headers() method for files such as CSS and JS
        **/
        public static function file($path, $download = false, $status = 200) {
            $response = new Response;
            
            if(file_exists(root("$path"))) {
                
                $response->status($status, get_mime_type(root($path)));
            
                if($download):
                    $response->headers(array(
                        'Content-Disposition' => 'attachment; filename="'.basename($path).'"',
                        'Pragma' => 'public',
                        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                    ));
                else:
                    $response->headers(array(
                        'Content-Disposition' => 'inline; filename="'.basename($path).'"'
                    ));
                endif;
                
                $response->content = function() use ($path) {  
                    readfile(root($path));
                };
                
                return $response;
                    
            }
            else {
                return self::view('404', 404);
            }            
        }
        
        
        /**
         * Define binary data response
         * @param mixed     $data
         * @param integer   $status
        **/
        public static function binary($data, $status = 200) {
            $response = new Response;
            $response->status($status, 'application/octet-stream');
            $response->content = $data;
            return $response;
        }
        
        /**
         * Define an empty response
         * @param integer   $status
        **/
        public static function null($status = 200) {
            $response = new Response;
            $response->status($status, 'text/plain');
            $response->content = null;
            return $response;
        }
        
        /**
         * Output response
         * (execute last defined response)
        **/
        public function render() {
            // Send headers
            http_response_code($this->code);
            foreach($this->headers as $name => $value) {
                @header("$name: $value", true);
            }
            
            
            // Output content
            if(is_closure($this->content)):
                call_user_func($this->content, array($this));

            else:
                if(is_closure($this->data)) // Generate data value
                    $this->data = call_user_func($this->data);
            
                if(is_closure($this->handler)) // Apply a callback
                   $this->content = call_user_func($this->handler, $this->content, $this->data, $this->code);
                            
                echo $this->content;
            
            endif;
                        
        }
        
    }
    
?>