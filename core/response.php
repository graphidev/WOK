<?php
    
    /**
     * Define Response headers and body.
     * It also manage view file caching and
     * routers caching for every response type
     *
     * @package Core
    **/
    class Response {
        
        private static $headers     = array();
        private static $content     = null;
        private static $code        = 200;
        private static $data        = array();
        private static $handler     = null;
        private static $cachetime   = null;
        private static $cachefile   = null;
        
        
        private static $mimes = array(
            'css' => 'text/css', 
            'js' => 'application/javascript'
        );
        
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
         * Define headers.
         * Custom headers must begin with X-
         * @param array     $headers
        **/
        public static function headers($headers) {
            self::$headers = array_merge($headers, self::$headers);
        }
                
        /**
         * Define response header status (with HTTP code)
         * @param integer   $code
         * @param string    $type
        **/
        public static function status($code, $type = null) {
            self::$code = $code;
                
            if(!empty($type))  
                self::headers(array('Content-type' => $type));
        }
        
        /**
         * Define data to use for response. 
         * A closure function as parameter will be executed 
         * provided that cached file does not exists.
         * @param mixed   $data
        **/
        public static function assign($data) {
            if(is_array($data))
                self::$data = array_merge(self::$data, $data);
            else
                self::$data = $data;
        }
        
        
        /**
         * Define response content handler
         * @param closure   $function
        **/
        public static function handler($function) {
            if(!is_closure($function))
                trigger_error('Parameter in Response::handler must be a function', E_USER_ERROR);
            
            self::$handler = $function;
        }        
        
        
        /**
         * Redirect permanently or temporarily
         *
         * @param string    $target
         * @param boolean   $permanent
        **/
        public static function redirect($target, $permanent = false) {       
            self::$code = ($permanent ? 301 : 302);
            self::headers(array('Location'=> $target));
        }
        
        
        /**
         * Generate response cache
         * Use headers and file
         * @param integer   $time
         * @param string    $status
         * @param mixed     $file
        **/
        public static function cache($time = self::CACHETIME_SHORT, $status = self::CACHE_PROTECTED, $file = false) {
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
            
            self::headers($headers); 
            
            if($file && !SYSTEM_DEBUG): // Cache file
                self::$cachetime = $time;
            
                if($status == self::CACHE_PROTECTED)
                    self::$cachefile = "$file-$suffix";
                else
                    self::$cachefile = $file;
            endif;
            
        }        
        
        /**
         * Define a view response
         * @param string    $template
         * @param integer   $status
        **/
        public static function view($template, $status = 200) {
            self::status($status, 'text/html; charset=utf8');
            
            self::$content = function() use($template) {
                
                // Update cache file path
                self::$cachefile .= '.html'; 
                       
                // Output cached view
                if(!empty(self::$cachetime) && Cache::exists(self::$cachefile, self::$cachetime) 
                   && Cache::time(self::$cachefile) > filemtime(root(PATH_TEMPLATES."/$template.php"))):
                
                    Cache::get(self::$cachefile);

                else: // Generate view
                    
                    // Execute data's requests
                    if(is_closure(self::$data)): 
                        self::$data = call_user_func(self::$data);
                    endif;

                    // Generate cache view
                    ob_start(function($buffer, $phase) {
                        
                        if(!is_null(self::$handler)):
                            $buffer = call_user_func(self::$handler, $buffer, self::$data, self::$code);
                        endif;
                        
                        if(!empty(self::$cachetime))
                            Cache::register(self::$cachefile, $buffer);
                        
                        self::$content = $buffer;                                      
                        return $buffer;
                    });
                    
                    // Parse template file
                    View::parse($template, self::$data);
                    
                    ob_end_flush();


                endif; 
            
            };  
        
        }
        
        /**
         * Define JSON response
         * @param array     $data
         * @param integer   $status
        **/
        public static function json(array $data, $status = 200) {
            self::status($status, 'application/json; charset=utf-8');
            
            self::$content = function() use($data) {
                
                // Update cache file path
                self::$cachefile .= '.json';
                
                if(!empty(self::$cachetime) && Cache::exists(self::$cachefile, self::$cachetime)) {
                    
                     Cache::get(self::$cachefile);
                    
                }
                else {
                
                    // Execute data's requests
                    if(is_closure(self::$data)): 
                        self::$data = call_user_func(self::$data);
                    endif;
                    
                    if(!empty($data))
                        self::$data = array_merge(self::$data, $data);
                    
                    $json = json_encode(self::$data);
                    
                    if(!empty(self::$cachetime))
                        Cache::register(self::$cachefile, $json);
                    
                    echo $json;
                
                }
  
            };
        }
        
        
        /**
         * Define XML response
         * @param array     $array
         * @param integer   $status
        **/
        public static function xml(array $data = null, $status = 200) {
            self::status($status, 'application/xml; charset=utf-8');
            
            self::$content = function() use($data) {
                
                // Update cache file path
                self::$cachefile .= '.xml';
                
                if(!empty(self::$cachetime) && Cache::exists(self::$cachefile, self::$cachetime)) {
                    
                     Cache::get(self::$cachefile);
                    
                }
                else {
                
                    // Execute data's requests
                    if(is_closure(self::$data)): 
                        self::$data = call_user_func(self::$data);
                    endif;
                    
                    if(!empty($data))
                        self::$data = array_merge(self::$data, $data);
                    
                    $xml = xml_encode(self::$data, 'document');
                    
                    if(!empty(self::$cachetime))
                        Cache::register(self::$cachefile, $xml);
                    
                    echo $xml;
                
                }
  
            };
        }
        
        
        /**
         * Define text response
         * @param string    $string
         * @param integer   $status
        **/
        public static function text($string, $status = 200) {
            self::status($status, 'text/plain; charset=utf-8');
            self::$content = function() use($string) {
                
                // Update cache file path
                self::$cachefile .= '.txt';
                
                if(!empty(self::$cachetime) && Cache::exists(self::$cachefile, self::$cachetime)) {
                    
                     Cache::get(self::$cachefile);
                    
                }
                else {
                
                    // Execute data's requests
                    if(!empty($string)):
                        self::$data = $string;
                    
                    elseif(is_closure(self::$data)): 
                        self::$data = call_user_func(self::$data);
                    endif;

                    if(!empty(self::$cachetime))
                        Cache::register(self::$cachefile, self::$data);
                    
                    echo self::$data;
                
                }
  
            };
        }
        
        
        /**
         * Define file response (also can force download)
         * @param string    $path
         * @param boolean   $download
         * @param integer   $status
        **/
        public static function file($path, $download = false, $status = 200) {
            
            if(file_exists(root("$path"))):
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $mime = !empty(self::$mimes[$extension]) ? self::$mimes[$extension] : get_mime_type(root("$path"));
            
                self::status($status, $mime);
            
                if($download):
                    header('Content-Disposition: attachment; filename="'.basename($path).'"');
                    self::headers(array(
                        'Pragma' => 'public',
                        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                    ));
                else:
                    header('Content-Disposition: inline; filename="'.basename($path).'"');
                endif;
                
                self::$content = function() use ($path) {  
                    readfile(root("$path"));
                };
            
                    
            else:
                self::view('404', 404, true);
            endif;            
        }
        
        
        /**
         * Define binary data response
         * @param mixed     $data
         * @param integer   $status
        **/
        public static function binary($data, $status = 200) {
            self::status($status, 'application/octet-stream');
            self::$content = $data;
        }
        
        /**
         * Output response
         * (execute last defined response)
        **/
        public static function output() {
            
            
            // Send headers
            http_response_code(self::$code);
            foreach(self::$headers as $name => $value) {
                @header("$name: $value", true);
            }    
            
            // Output content
            if(is_closure(self::$content)):
                call_user_func(self::$content);
            
            else:
                if(is_closure(self::$data))
                    self::$data = call_user_func(self::$data);
            
                if(is_closure(self::$handler))
                   self::$content = call_user_func(self::$handler, self::$content, self::$data, self::$code);

                echo self::$content;
            
            endif;
                        
        }
        
    }
    
?>