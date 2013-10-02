<?php
    
    class  Response {
        private static $data = array();
        
        private static $types = array(
            'default'   => 'text/html; charset=utf-8',
            'text'      => 'text/plain; charset=utf-8',
            'html'      => 'text/html; charset=utf-8',
            'json'      => 'application/json; charset=utf-8',
            'xml'       => 'application/xml; charset=utf-8',
            'binary'    => 'application/octet-stream',
            
        );
        
        private static $codes = array(
            100 => "Continue", 
            101 => "Switching Protocols", 
            102 => "Processing", 
            200 => "OK", 
            201 => "Created", 
            202 => "Accepted", 
            203 => "Non-Authoritative Information", 
            204 => "No Content", 
            205 => "Reset Content", 
            206 => "Partial Content", 
            207 => "Multi-Status", 
            300 => "Multiple Choices", 
            301 => "Moved Permanently", 
            302 => "Found", 
            303 => "See Other", 
            304 => "Not Modified", 
            305 => "Use Proxy", 
            306 => "(Unused)", 
            307 => "Temporary Redirect", 
            308 => "Permanent Redirect", 
            400 => "Bad Request", 
            401 => "Unauthorized", 
            402 => "Payment Required",
            403 => "Forbidden", 
            404 => "Not Found", 
            405 => "Method Not Allowed", 
            406 => "Not Acceptable", 
            407 => "Proxy Authentication Required", 
            408 => "Request Timeout", 
            409 => "Conflict", 
            410 => "Gone", 
            411 => "Length Required", 
            412 => "Precondition Failed", 
            413 => "Request Entity Too Large", 
            414 => "Request-URI Too Long", 
            415 => "Unsupported Media Type", 
            416 => "Requested Range Not Satisfiable", 
            417 => "Expectation Failed", 
            418 => "I'm a teapot", 
            419 => "Authentication Timeout", 
            420 => "Enhance Your Calm", 
            422 => "Unprocessable Entity", 
            423 => "Locked", 
            424 => "Failed Dependency", 
            424 => "Method Failure", 
            425 => "Unordered Collection", 
            426 => "Upgrade Required", 
            428 => "Precondition Required", 
            429 => "Too Many Requests", 
            431 => "Request Header Fields Too Large", 
            444 => "No Response", 
            449 => "Retry With", 
            450 => "Blocked by Windows Parental Controls", 
            451 => "Unavailable For Legal Reasons", 
            494 => "Request Header Too Large", 
            495 => "Cert Error", 
            496 => "No Cert", 
            497 => "HTTP to HTTPS", 
            499 => "Client Closed Request", 
            500 => "Internal Server Error", 
            501 => "Not Implemented", 
            502 => "Bad Gateway", 
            503 => "Service Unavailable", 
            504 => "Gateway Timeout", 
            505 => "HTTP Version Not Supported", 
            506 => "Variant Also Negotiates", 
            507 => "Insufficient Storage", 
            508 => "Loop Detected", 
            509 => "Bandwidth Limit Exceeded", 
            510 => "Not Extended", 
            511 => "Network Authentication Required", 
            598 => "Network read timeout error", 
            599 => "Network connect timeout error"
        );  
        
        const CACHE_PUBLIC          = 'PUBLIC'; // Cache public
        const CACHE_PROTECTED       = 'PROTECTED'; // Cache for autheticaed only
        const CACHE_PRIVATE         = 'PRIVATE'; // Cache private
        const CACHETIME_NEVER       = 0; // Do not cache
        const CACHETIME_LOW         = 1800; // 5 minutes
        const CACHETIME_MEDIUM      = 2592000; // 12 hours
        const CACHETIME_LONG        = 5184000; // 1 day
        
        
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
        public static function status($type, $code) {
            if(!empty($types[$type]))
                $type = $types[$type];
            else
                $type = (!empty($type) ? $type : $types['default']);
            
            if($type == 'binary')
                header('Content-Transfer-Encoding: binary');
            
            header("HTTP/1.1 $code " .self::$codes[$code], true, $code);
            header("Content-type: $type", true, $code);
        }
        
        /**
         * Send Cache headers
        **/
        public static function cache($time = self::CACHETIME_LOW, $status = self::CACHE_PROTECTED) {
            // Private cache : do not cache
            if(!$time || $status == self::CACHE_PRIVATE):
                $arguments = array(
                    'private', // Private resource, do not cache
                    'no-cache', // Never cache resource
                    'no-store', // Never cache resource, even on hard drive
                    'must-revalidate', // Always check resource
                    'proxy-revalidate', // Always Check resource, even middle proxys
                    
                );
                header("Pragma: no-cache");
            
            // Protected or public cache
            elseif($time):
                $arguments = array(
                    "max-age=$time", // Max resource age (for browsers)
                    "s-maxage=$time", // Max resource age (for middle caches)
                );
                if($status == self::CACHE_PROTECTED):
                    $arguments[] = 'public, no-cache, must-revalidate';
                    header("Pragma: no-cache");
                else:
                    $arguments[] = 'public';
                    header("Pragma: cache");
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
         * define datas
        **/
        public static function assign($data) {
            if(is_array($data))
                self::$data = array_merge(self::$data, $data);
            else
                self::$data = $data;
        }
        
        
        /**
         * Redirect permanently or not (exit script)
        **/
        public function redirect($target, $permanent = false) {
            $code = ($permanent ? 301 : 302);
            header("HTTP/1.1 $code ".self::$codes[$code], false, $code);
            header("Location: $target");
            
            Console::register(); // Register logs before exit
            exit;
        }
        
        
        /**
         * Call a view file
        **/
        public static function view($template, $status = 200) {  
            extract(self::$data);
            ob_start();
                
            if(file_exists(root(PATH_TEMPLATES."/$template.php")) && $template != '404'):  
                self::status('html', $status);
                include_once(root(PATH_TEMPLATES."/$template.php"));
                    
                
            else:
                self::status('html', 404);
                
                if(file_exists(root(PATH_TEMPLATES."/404.php"))):
                    include_once(root(PATH_TEMPLATES."/404.php"));
                
                else:
                    if($template != '503')
                        Console::log("$template template does not exists", Console::LOG_WARNING, true);
                    else
                        Console::log("$template template does not exists", Console::LOG_ERROR);
                
                endif;
                
            endif;
                
            // Generate output
            $buffer = ob_get_flush();
            if(ob_end_clean()):
                echo $buffer;
            else:
                ob_end_flush();
                Console::warning("The buffer view haven't been destroyed properly for '$template'");
            endif;
        }
        
        
        /**
         * Send JSON data
        **/
        public static function json($data = null, $status = 200) {
            self::status('json', $status);
            echo json_encode(!empty($data) ? $data : self::$data);
        }
        
        
        /**
         * Send XML data
        **/
        public static function xml($data = null, $status = 200) {
            self::status('xml', $status);
            echo xml_encode(!empty($data) ? $data : self::$data, 'document');
        }
        
        
        /**
         * Send text
        **/
        public function text($string, $status = 200) {
            self::status('text', $status);
            echo $string;
        }
        
        
        /**
         * Send a file (also can be downloaded)
        **/
        public static function file($path, $download = false, $status = 200) {
            if(file_exists(root("/$path"))):
                $mime = \Compatibility\get_mime_type(root("/$path"));
                self::status($mime, $status);
            
                if($download)
                    header('Content-Disposition: attachment; filename="'.basename($path).'"');
                    
                readfile(root("/$path"));
            
            else:
                self::view('404', 404);
            endif;            
        }
        
        
        /**
         * Send binary datas
        **/
        public static function binary($data, $status = 200) {
            self::status('binary', $status);
            echo $data;
        }
        
    }
    
?>