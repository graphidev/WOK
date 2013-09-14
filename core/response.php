<?php
    
    class  Response {
        
        private static $base = '/';
        public static $data = array();
        
        
        /**
         * Redirect permanently or not
        **/
        public function redirect($target, $permanent = true) {
            $code = ($permanet ? 301 : 302);
            $message = ($permanet ? 'Moved permanently' : 'Moved Temporarily');
            header("HTTP/1.1 $code $message", false, $code);
            header("Location: $target");
            exit();
        }
        
        /**
         * Call a view file
        **/
        public static function view($name) {
            self::$base = dirname(PATH_TEMPLATES."/$name");
            Response::type('html');

            if(file_exists(root(PATH_TEMPLATES."/$name.php"))):
                header("HTTP/1.1 200 OK");
                include_once(root(PATH_TEMPLATES."/$name.php"));
                
            
            else:
                header("HTTP/1.1 404 Not found");
            
                if(file_exists(root(PATH_TEMPLATES."/404.php")))
                    include_once(root(PATH_TEMPLATES."/404.php"));
            
                else
                    exit("404 Document not found");
            
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
        public static function type($type) {
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
                    $type = $type;
            }
            header("Content-type: $type");
        }
        
        /**
         * Return custom datas
         * Working with custom response type
        **/
        public static function assign($data, $object = false) {
            if(is_array($data) && $object)
                self::$data = json_decode(json_encode($data), false);
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

    function _e($path, $data = array()) {
        return Locales::_e($path, $data);
    }
    function _t($path, $data = array()) {
        echo _e($path, $data);
    }

?>