<?php
    
    class  Response extends Request {
        
        private static $base = '/';
        public static $data = array();
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
        
        
        /**
         * Redirect permanently or not
        **/
        public function redirect($target, $permanent = true) {
            $code = ($permanent ? 301 : 302);
            $message = ($permanet ? 'Moved permanently' : 'Moved Temporarily');
            header("HTTP/1.1 $code ".self::$codes[$code], false, $code);
            header("Location: $target");
            exit();
        }
        
        
        /**
         * Call a view file
        **/
        public static function view($template, $cache = false) {
            self::$base = dirname(PATH_TEMPLATES."/$template");
            
            ob_start();
            
            if(file_exists(root(PATH_TEMPLATES."/$template.php")) && $template != '404'):
                Response::type('html', 200);
                include_once(root(PATH_TEMPLATES."/$template.php"));
                
            
            else:
                Response::type('html', 404);
            
                if(file_exists(root(PATH_TEMPLATES."/404.php"))):
                    include_once(root(PATH_TEMPLATES."/404.php"));
            
                else:
                    if($template != '503')
                        Console::log("$template template does not exists", Console::LOG_WARNING, true);
                    else
                        Console::log("$template template does not exists", Console::LOG_ERROR);
            
                endif;
            
            endif;
            
            $buffer = &ob_get_flush();
            if(ob_end_clean()):
                echo Template::generate($buffer, $template);
            else:
                ob_end_flush();
                Console::warning("The buffer view haven't been destroyed properly for '$template'");
            endif;
        }
        
        
        /**
         * Call a static file
        **/
        public static function resource($name) {
            self::$base = PATH_FILES;
            if(file_exists(root(PATH_FILES."/$name"))):
                Response::type(\Compatibility\get_mime_type(root(PATH_FILES."/$name")));
                readfile(root(PATH_FILES."/$name"));
            else:
                Response::view('404');
            endif;
        }
        
        /**
         * Define the response type
        **/
        public static function type($type, $code = 200) {
            switch($type) {
                case 'text':
                    $type = 'text/plain';
                    break;
                case 'html':
                    $type = 'text/html';
                    break;
                case 'json':
                    $type = 'application/json';
                    break;
                case 'xml':
                    $type = 'application/xml';
                    break;
                default:
                    $type = (!empty($type) ? $type : 'text/html');
            }
            header("Content-type: $type", true, $code);
            header("HTTP/1.1 $code " .self::$codes[$code], true, $code);
        }
        
        
        /**
         * Return custom datas
         * Working with custom response type
        **/
        public static function assign($data, $object = false) {
            if(is_array($data) && $object)
                self::$data = json_decode(json_encode($data), true);
            else
                self::$data = $data;
        }
                
        
        /**
         * Include a common element from the current base
        **/
        public static function inc($name, $base = null) {
            
            if(empty($base)) // Redefine base
                $base = self::$base;
                       
            if(file_exists(root("$base/$name.php")))
                include(root("$base/$name.php"));
        }
        
    }
    
    
    /**
     * Locales functions
    **/
    function _e($path, $data = array()) {
        return Locales::_e($path, $data);
    }
    function _t($path, $data = array()) {
        echo _e($path, $data);
    }

?>