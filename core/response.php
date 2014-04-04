<?php
    
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
         * Send custom headers
         * Custom headers must begin with X-
         * @param array     $headers
        **/
        public static function headers($headers) {
            self::$headers = array_merge($headers, self::$headers);
        }
                
        /**
         * Define response header status
         * @param integer   $code
         * @param string    $type
        **/
        public static function status($code, $type = null) {
            self::$code = $code;
                
            if(!empty($type))  
                self::headers(array('Content-type' => $type));
        }
        
        /**
         * Define data to use for response
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
        public function handler($function) {
            if(!is_function($function))
                trigger_error('Parameter in Response::handler must be a function', E_USER_ERROR);
            
            self::$handler = $function;
        }        
        
        
        /**
         * Redirect permanently or not
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
            if(!$time || $status == self::CACHE_PRIVATE):
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
                    $headers['Cache-Control'] .= 'public';
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
                self::$cachefile = root(PATH_CACHE."/$file-".Session::get('language').".html");
            endif;
            
        }        
        
        /**
         * Call a view file
         * @param string    $template
         * @param integer   $status
        **/
        public static function view($template, $status = 200) {
            self::status($status, 'text/html; charset=utf-8');
            
            if(!file_exists(root(PATH_TEMPLATES."/$template.php")))
                trigger_error("Template $template not found", E_USER_ERROR);
            
            self::$content = function() use($template) {
                
                // Output cached view
                if(!empty(self::$cachetime) && file_exists(self::$cachefile) 
                   && filemtime(self::$cachefile) > filemtime(root(PATH_TEMPLATES."/$template.php"))
                   && filemtime(self::$cachefile) <= time() + self::$cachetime):
                    
                    http_response_code(304);
                    header('Content-type: text/html; charset=utf8', true, 304);
                    readfile(self::$cachefile);

                else: // Generate view
                    
                    // Execute data's requests
                    if(is_function(self::$data)): 
                        $execute = self::$data;
                        self::$data = $execute();
                    endif;

                    // Generate cache view
                    ob_start(function($buffer, $phase) {
                        if(!empty(self::$cachetime))
                            file_put_contents(self::$cachefile, $buffer);   

                        self::$content = $buffer;
                        return $buffer;
                    });

                    extract(self::$data);
                    include root(PATH_TEMPLATES."/$template.php");
                
                    ob_end_flush();


                endif; 
            
            };  
        
        }
        
        /**
         * Send JSON data
         * @param array     $data
         * @param integer   $status
        **/
        public static function json(array $data, $status = 200) {
            self::status($status, 'application/json; charset=utf-8');
            self::$content = json_encode(!empty($data) ? $data : self::$data);
        }
        
        
        /**
         * Send XML data
         * @param array     $array
         * @param integer   $status
        **/
        public static function xml(array $array = null, $status = 200) {
            if(!empty($array))
                self::$data = $array;
            
            self::status($status, 'application/xml; charset=utf-8');
            self::$content = xml_encode(self::$data, 'document');
        }
        
        
        /**
         * Send text
         * @param string    $string
         * @param integer   $status
        **/
        public static function text($string, $status = 200) {
            self::status($status, 'text/plain; charset=utf-8');
            self::$content = $string;
        }
        
        
        /**
         * Send a file (also can force download)
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
                
                self::$content = function() {            
                    readfile(root("$path"));
                };
            
                    
            else:
                self::view('404', 404, true);
            endif;            
        }
        
        
        /**
         * Send binary datas
         * @param mixed     $data
         * @param integer   $status
        **/
        public static function binary($data, $status = 200) {
            self::status($status, 'application/octet-stream');
            self::$content = $data;
        }
        
        /**
         * Output response
        **/
        public static function output() {
            
            // Send headers
            http_response_code(self::$code);
            foreach(self::$headers as $name => $value) {
                @header("$name: $value", true);
            }
                        
            // Apply treatment on content
            if(is_function(self::$handler)):
                $execute = self::$handler;
                $execute(self::$content, self::$data, self::$code);
            endif;
            
            // Output content
            if(is_function(self::$content)):
                $execute = self::$content;
                $execute();
            
            else:
                echo self::$content;
            
            endif;
                        
        }
        
    }
    
?>