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

    use \Message\Components\Stream;

    /**
     * The Response class provides
     * an interface to generate a HTTP response
    **/
    class Response extends Message {


        /**
         * Instanciate a new response
         * @param   mixed       $body           Response body
         * @param   integer     $status         Response status code
        **/
        public function __construct($body = null, $status = 200) {

            $this->code = $status;
            $headers    = new Components\Headers();

            if(!is_resource($body)) {
                $stream = fopen('php://temp', 'w+');
                fputs($stream, $body);
                rewind($stream);

                $body = $stream;
            }

            $stream = new Stream($body);


            parent::__construct($stream, $headers);


        }


        /**
         * Instanciate a redirection response
         * @param   string      $target         Redirection target
         * @param   integer     $code           HTTP redirection code
        **/
        static public function redirect($target, $code = 302) {
            $response = new self(null, $code);
            $response->headers->addHeader('Location', $target);
            return $response;
        }


        /**
         * Instanciate an empty response
         * @param   integer     $code           HTTP redirection code
        **/
        static public function null($code) {
            $response = new self(null, $code);
            return $response;
        }

        /**
         * Instanciate a text response
         * @param   string      $body           Response body
         * @param   integer     $code           Response status code
        **/
        static public function text($body, $code = 200) {

            $response = new self($body, $code);
            $response->headers->addHeader('Content-Type', 'text/plain; charset=utf-8');

            return $response;

        }


        /**
         * Instanciate an HTML response
         * @param   string      $body           Response body
         * @param   integer     $code           Response status code
        **/
        static public function html($body, $code = 200) {

            $response = new self($body, $code);
            $response->headers->addHeader('Content-Type', 'text/html; charset=utf-8');

            return $response;

        }

        /**
         * Instanciate an HTML response
         * @param   string|array       $body           Response body
         * @param   integer            $code           Response status code
        **/
        static public function json($body, $code = 200) {

            if(is_array($body))
                $body = json_encode($body);

            $response = new self($body, $code);
            $response->headers->addHeader('Content-Type', 'text/html; charset=utf-8');

            return $response;

        }

        /**
         * Instanciate a file response
         * @param   string      $filepath           Response file path or resource
         * @param   string      $name               Response file name
         * @param   boolean     $download           Set the download response headers
         * @param   integer     $code               Response status code
        **/
        static public function file($filepath, $name = null, $download = false, $code = 200) {

            if(!is_readable($filepath))
                throw new \RuntimeException('Not readable file '.$file);

            if(is_resource($filepath)) {
                $stream = $filepath;
            }
            else {
                $stream   = fopen($filepath, 'r');
                $response = new self($stream, $code);
            }

            if(empty($name)) $name = basename($path);

            $mime = get_mime_type($filepath);
            if(!$mime) $mime = 'application/octet-stream';

            $response->setHeader('Content-Type', $mime);
            $response->setHeader('Content-Transfer-Encoding', 'Binary');
            $response->setHeader('Content-Length', $response->body->getSize());

            $response->setHeader('Content-Disposition',
                ($download ? 'attachment; filename="'.$name.'"' : 'inline; filename="'.$name.'"')
            );

            return $response;

        }

        /**
         * Define binary data response
         * @param mixed     $data           Response binary data
         * @param integer   $status         Response status code
        **/
        public static function binary($data, $status = 200) {

            $response = new Response($data, $status);
            $response->headers->addHeadear('Content-Type', 'application/octet-stream');
            return $response;

        }


        /**
         * Check if a header has already been set
         * @param   string      $name       Header name
        **/
        public function hasHeader($name) {
            return $this->headers->hasHeader($name);
        }

        /**
         * Set a new header value (override if already exists)
         * @param   string      $name       Header name
         * @param   string      $value      Header value
        **/
        public function setHeader($name, $value) {
            return $this->headers->setHeader($name, $value);
        }

        /**
         * Add a header value
         * @param   string      $name       Header name
         * @param   string      $value      Header value
        **/
        public function addHeader($name, $value) {
            return $this->headers->addHeader($name, $value);
        }


        /**
         * Define cache etag associated headers
         * @param   string      $etag           Etag key
         * @param   integer     $modified       Last modification time
        **/
        public function setEtagCache($etag, $modified = null) {

            $this->headers->addHeader('Etag', $etag);

            if(!empty($modified)) {
                $date = new \DateTime(date('r', $modified));
                $date->setTimezone(new \DateTimeZone('GMT'));
                $this->headers->addHeader('Last-Modified', $date->format('r'));
            }

        }

        /**
         * Define the cache type
         * @param   string      $type       Caching life time
        **/
        public function setCacheType($time) {

            $headers = array(
                'Cache-Control'  => 'private, no-cache, no-store, must-revalidate, proxy-revalidate',
                'Pragma'         => 'no-cache'
            );

            $headers['Cache-Control'] =  "max-age=$time, s-maxage=$time";
            $headers['Cache-Control'] .= ', public, no-cache, must-revalidate';
            $headers['Pragma'] = 'no-cache';

            $headers['Cache-Control'] .= ', public';
            $headers['Pragma'] = 'cache';

            $this->headers->addHeader('Expires', $date->format('r'));

        }



        /**
         * Define the response cache headers
         * @param   integer         $timeleft           Left cache time
         * @param   boolean         $public             Is the response body public (true) or private (false) ?
        **/
        public function setCacheHeaders($timeleft = 0, $public = true) {

            // Expiration date
            $date = new \DateTime(date('r', time()+$timeleft));
            $date->setTimezone(new \DateTimeZone('GMT'));
            $this->headers->setHeader('Expires', $date->format('r'));

            // Cache life time
            $this->headers->addHeader('Cache-Control', [
                'max-age='.$timeleft, 's-maxage='.$timeleft, 'max-stale=0'
            ]);

            $this->headers->setHeader('Pragma',         ($timeleft ? 'cache' : 'no-cache'));
            $this->headers->addHeader('Cache-Control',  ($public ? 'public' : 'private'));

        }


        /**
         * @param   string          $etag               Cached value key
         * @param   integer         $lastModified       Last cache value modification time
        **/
        public function setCacheEtag($etag, $lastModified) {

            if($lastModified) {
                $mdate = new \DateTime(date('r', $lastModified));
                $mdate->setTimezone(new \DateTimeZone('GMT'));
                $this->headers->setHeader('Last-Modified', $mdate->format('r'));
            }

            $this->headers->setHeader('Etag', $etag);
            $this->headers->setHeader('Pragma', ($timeleft ? 'cache' : 'no-cache'));

        }


        /**
         * Render and send the HTTP response
        **/
        public function __invoke() {

            http_response_code($this->code);

            $date = new \DateTime(date('r', time()));
            $date->setTimezone(new \DateTimeZone('GMT'));

            // Set auto calculated headers
            $this->headers->setHeader('Date', $date->format('r'));
            $this->headers->setHeader('Content-Length', $this->body->getSize());

            // Prevent caching by default
            if(!$this->headers->hasHeader('Cache-Control')) {
                $this->headers->setHeader('Pragma', 'no-cache');
                $this->headers->addHeader('Cache-Control', [
                    'private', 'no-cache', 'no-store',
                    'max-age=0', 's-maxage=0', 'max-stale=0',
                    'must-revalidate', 'proxy-revalidate'
                ]);
            }

            foreach($this->headers as $name => $value) {
                @header(sprintf('%s: %s', $name, $value), true);
            }

            stream_copy_to_stream(
                $this->body->getStream(),
                fopen('php://output', 'w+')
            );


        }


        /**
         * Convert response to string
        **/
        public function __toString() {

            /*
            $output = sprintf(
                'HTTP/%s %s %s',
                $this->environment->getProtocolVersion(),
                $this->code,
                $this->status
            );
            $output .= PHP_EOL;
            */


            foreach ($this->headers as $name => $value) {
                $output .= sprintf('%s: %s', $name, $value) . PHP_EOL;
            }
            $output .= PHP_EOL;

            $output .= (string)$this->body->getContents();

            return $output;

        }



    }
