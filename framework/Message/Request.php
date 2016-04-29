<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Message;

    use \Message\Components\Uri;
    use \Message\Components\Stream;
    use \Message\Components\Headers;

    /**
     * The Request class provide an
     * interface to manipulate HTTP request
    **/
    class Request extends Message {

        /**
         * @var Uri         $uri            Uri interface
        **/
        protected $uri;

        /**
         * @var string      $method         HTTP request method
        **/
        protected $method;


        /**
         * @var array       $files          Files collection
        **/
        protected $files = array();


        /**
         * Instanciate the request interface
         * @note This method auto recover HTTP request informations
        **/
        public function __construct() {

            $this->method = mb_strtoupper($_SERVER['REQUEST_METHOD']);

            $user          = (isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '');
            $password      = (isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '');
            $auth          = $user.':'.$password;

            // URI
            $this->uri     = new Uri(
                (!empty($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http').'://'
                .(!empty($password) && !empty($user) ? $auth.'@' : '')
                .$_SERVER['HTTP_HOST']
                .(!empty($_SERVER['SERVER_PORT']) ? ':'.$_SERVER['SERVER_PORT'] : '')
                .$_SERVER["REQUEST_URI"]
            );

            // Headers
            $this->headers = new Headers(getallheaders());


            // Body
            $stream = fopen('php://temp', 'w+');
            stream_copy_to_stream(fopen('php://input', 'r+'), $stream);
            rewind($stream);

            // Fix PHP input body for submited form
            if($this->method == 'POST' && in_array($this->getMediaType(), ['application/x-www-form-urlencoded', 'multipart/form-data'])) {
                $stream = fopen('php://temp', 'w+');
                fwrite($stream, json_encode($_POST));
                rewind($stream);
            }

            $this->body    = new Stream($stream);

            // new \Components\File($path, $name, $type, $size, $error);
            foreach($_FILES as $k => $f) {

                // Single item file
                if(!is_array($f['name'])) {
                    $this->files[$k] = $f;
                }

                // Multi items files
                else {

                    foreach($f['name'] as $mk => $mn) {
                        $this->files[$k][$mk] = array(
                            'name'      => $mn,
                            'type'      => $f['type'][$mk],
                            'size'      => $f['size'][$mk],
                            'error'     => $f['error'][$mk],
                            'tmp_name'  => $f['tmp_name'][$mk],
                        );
                    }

                }

            }

        }

        /**
         * Get the HTTP method
         * @return string
        **/
        public function getMethod() {
            return $this->method;
        }

        /**
         * Get the request URI interface
         * @return \Message\Components\Uri
        **/
        public function getURI() {
            return $this->uri;
        }

        /**
         * Reassign an URI component
         * @param \Message\Components\Uri   $uri        URI component to assign
        **/
        public function withUri(Uri $uri) {
            $this->uri = $uri;
        }

        /**
         * Get the Request headers list
         * @return array
        **/
        public function getHeaders() {
            return $this->headers->__toArray();
        }

        /**
         * Check a header availability
         * @param   string      $name           Header name
         * @return  boolean
        **/
        public function hasHeader($name) {
            return $this->headers->hasHeader($name);
        }

        /**
         * Get a header value
         * @param   string              $name           Header name
         * @param   boolean|string      $default        Header default value
         * @return  string
        **/
        public function getHeader($name, $default = false) {
            return $this->headers->getHeader($name, $default);
        }


        /**
         * Get a multiple header values
         * @param string    $name            Header name
         * @param array     $default         Alternative default header values
        **/
        public function getHeaderValues($name, array $default = null) {
            return $this->headers->getHeaderValues($name, $default);
        }

        /**
         * Get a multiple header decreasingly ordered values
         * @param string    $name            Header name
         * @param array     $default         Alternative default header values
        **/
        public function getHeaderOrderedValues($name, array $default = null) {
            return $this->headers->getHeaderOrderedValues($name, $default);
        }

        /**
         * Get the user accepted language
        **/
        public function getAcceptedLanguages() {
            return $this->headers->getHeaderValues('Accept-Language');
        }

        /**
         * Get the request body type
         * @return string
        **/
        public function getMediaType() {

            $ctype = $this->getHeader('Content-Type', null);
            $parts = preg_split('/\s*[;,]\s*/', $ctype);
            return mb_strtolower($parts[0]);

        }

        /**
         * Get the request body parameters
         * @return array
        **/
        public function getMediaParameters() {

            $ctype = $this->getHeader('Content-Type', null);
            $parts = preg_split('/\s*[;,]\s*/', $ctype);

            $length     = count($parts);
            $parameters = array();
            for ($i = 1; $i < $length; $i++) {
                list($name, $value) = explode('=', $parts[$i], 2);
                $parameters[mb_strtolower($name)] = $value;
            }

        }

        /**
         * Get the request body charset
         * @param   string      $default        Supposed charset value
         * @return  string
        **/
        public function getMediaCharset($default = null) {

            if(empty($default))
                $default = mb_internal_encoding();

            $parameters = $this->getMediaParameters();
            return (isset($parameters['charset']) ? $parameters['charset'] : $default);

        }

        /**
         * Get the request body stream
         * @return stream
        **/
        public function getBody() {
            return $this->body;
        }

        /**
         * Get the request parsed body
         * @return mixed
        **/
        public function getParsedBody() {

            $type       = $this->getMediaType();
            $body       = $this->body->getContents();
            $charset    = mb_strtoupper($this->getMediaCharset());

            // Get the right body charset
            if($charset != mb_strtoupper(mb_internal_encoding()))
                $body = mb_convert_encoding($body, $charset, mb_internal_encoding());

            // JSON
            if($type == 'application/json') {
                return json_decode($body, true);
            }

            // XML
            elseif($type == 'application/xml' || $type == 'text/xml') {
                $backup = libxml_disable_entity_loader(true);
                $xml = simplexml_load_string($body);
                libxml_disable_entity_loader($backup);
                return $xml;
            }

            // POST
            elseif(in_array($type, ['application/x-www-form-urlencoded', 'multipart/form-data'])) {
                return json_decode($body, true);
            }

            else {
                return $body;
            }

        }


        /**
         * Get the files interfaces collection
         * @return  array
        **/
        public function getFiles() {
            return $this->files;
        }

        /**
         * Get a file availability
         * @param   string      $name       File name (field)
         * @return  boolean
        **/
        public function hasFile($name) {
            return isset($this->files[$name]);
        }

        /**
         * Get a file availability
         * @param   string      $name       File name (field)
         * @return  \Message\Components\File
        **/
        public function getFile($name) {
            if(!$this->hasFile($name))
                return false;

            return $this->files[$name];
        }


        /**
         * Get the user IP address
        **/
        public function getIpAddress() {

            $methods = array(
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR'
            );


            foreach($methods as $key) {

                if(array_key_exists($key, $_SERVER) === true) {

                    foreach (explode(',', $_SERVER[$key]) as $ip) {

                        $ip = trim($ip); // trim for safety measures

                        // Validate IP
                        if( filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) )
                            return $ip;

                    }

                }

            }

            return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
        }


    }
