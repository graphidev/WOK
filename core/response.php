<?php
    
    class Response extends \Request {
        protected static $data = array();
        
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
         * Frame constants
        **/
        const FRAME_DENY            = 'DENY';
        const FRAME_ORIGIN          = 'SAMEORIGIN';
        
        
        /**
         * Send custom headers
         * Custom headers must begin with X-
        **/
        public static function headers($headers) {
            foreach($headers as $name => $value) {
                @header("$name: $value", true);
            }
        }
                
        /**
         * Define response status
        **/
        public static function status($code, $type = null) {
            http_response_code($code);
            
            if(!empty($type))
                header("Content-type: $type", true, $code);
        }
        
        /**
         * Iframe response configuration
        **/
        public static function frame($status = self::FRAME_ORIGIN) {
            if(is_array($status))
                header('X-Frame-Options: ALLOW-FROM '.implode(' ', $status));
            else
                header("X-Frame-Options : $status");
        }
        
        /**
         * Send Cache headers
        **/
        public static function cache($time = self::CACHETIME_SHORT, $status = self::CACHE_PROTECTED) {
            // Private cache : do not cache
            if(!$time || $status == self::CACHE_PRIVATE):
                $arguments = array(
                    'private', // Private resource, do not cache
                    'no-cache', // Never cache resource
                    'no-store', // Never cache resource, even on hard drive
                    'must-revalidate', // Always check resource
                    'proxy-revalidate', // Always Check resource, even middle proxys
                    
                );
                header("Pragma: no-cache", true);
            
            // Protected or public cache
            elseif($time):
                $arguments = array(
                    "max-age=$time", // Max resource age (for browsers)
                    "s-maxage=$time", // Max resource age (for middle caches)
                );
                if($status == self::CACHE_PROTECTED):
                    $arguments[] = 'public, no-cache, must-revalidate';
                    header("Pragma: no-cache", true);
                    header("Vary: Accept-Encoding", true);
                else:
                    $arguments[] = 'public';
                    header("Pragma: cache", true);
                    header("Vary: Accept-Encoding", true);
                endif;
                
                $date = new DateTime(date('r', time()+$time));
                $date->setTimezone(new DateTimeZone('GMT'));
                header("Expires: ".$date->format('r'), true);
            endif;
            
            // Send headers
            $arguments[] = 'no-transform'; // Never transform outputed data
            header('Cache-Control: '.implode(', ', $arguments), true);
        }
        
        
        /**
         * Redirect permanently or not (exit script)
        **/
        public static function redirect($target, $permanent = false) {        
            http_response_code($permanent ? 301 : 302);
            header("Location: $target");
            
            exit; // Prevent following script execution
        }
        
        
        /**
         * define datas
         * Working only with view method
        **/
        public static function assign($data) {
            if(is_array($data))
                self::$data = array_merge(self::$data, $data);
            else
                self::$data = $data;
        }
        
        
        /**
         * Call a view file
        **/
        public static function view($template, $status = 200, $cache = Response::DISABLE_CACHE) {            
            if(file_exists(root(PATH_TEMPLATES."/$template.php")) && $template != '404'):  
                self::status($status, 'text/html; charset=utf-8');
                self::_template($template, $cache);
                
            else:
                self::status(404, 'text/html; charset=utf-8');
                
                if(file_exists(root(PATH_TEMPLATES."/404.php"))):
                    self::_template('404', $cache);
                
                else:
                    if($template != '503')
                        trigger_error("$template template does not exists", E_USER_WARNING);
                    else
                        Console::log("$template template does not exists", Console::LOG_ERROR);
                
                endif;
                
            endif;
        }
        
        /**
         * Generate template output
        **/
        private static function _template($view, $cache) {
            $language = Session::language();
            $suffix = ($cache && !is_bool($cache) ? "-$cache" : '');
            $template = root(PATH_TEMPLATES."/$view.php");
            $cache = (!SYSTEM_DEBUG ? $cache : false);
            $cached = root(PATH_CACHE."/$view$suffix-$language.html");
            $time = (is_int($cache) ? $cache : Response::CACHETIME_SHORT);
            $overwrite = true;
            
                        
            ob_start(function($buffer, $phase) use(&$overwrite, $cached, $cache) {              
                // Overwrite cached file
                if($cache && $overwrite)
                    file_put_contents($cached, $buffer);
                
                return $buffer;
            }); // Keep output in a buffer
            
                if($cache && file_exists($cached) 
                   && filemtime($cached) > filemtime($template)
                   && filemtime($cached) <= time()+$time):
                    $overwrite = false;
                    readfile($cached);

                else:
                    extract(self::$data);
                    include_once($template);
            
                endif; 

            ob_end_flush(); // Generate output
        }
        
        /**
         * Send JSON data
        **/
        public static function json(array $data, $status = 200) {
            self::status($status, 'application/json; charset=utf-8');
            echo json_encode(!empty($data) ? $data : self::$data);
        }
        
        
        /**
         * Send XML data
        **/
        public static function xml($data = null, $status = 200) {
            self::status($status, 'application/xml; charset=utf-8');
            echo xml_encode((!empty($data) ? $data : self::$data), 'document');
        }
        
        
        /**
         * Send text
        **/
        public static function text($string, $status = 200) {
            self::status($status, 'text/plain; charset=utf-8');
            echo $string;
        }
        
        
        /**
         * Send a file (also can be downloaded)
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
                
                              
                readfile(root("$path"));
            
                    
            else:
                self::view('404', 404, true);
            endif;            
        }
        
        
        /**
         * Send binary datas
        **/
        public static function binary($data, $status = 200) {
            self::status($status, 'application/octet-stream');
            echo $data;
        }
        
    }
    
?>