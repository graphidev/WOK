<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Framework\Core;


    /**
     * Define Response headers and body.
     * It also manage view file caching and
     * routers caching for every response type
    **/
    class Response {

        private $headers     = array();
        private $content     = null;
        private $code        = 200;
        private $data        = array();
        private $handler     = null;
        private $cachetime   = null;
        private $cachefile   = null;
        private $fallback    = null;

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
        public function headers(array $headers) {
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
         * Apply a treatment on the response data
         * @param closure   $function
        **/
        public function handler(\Closure $function) {
            $this->handler = $function;
            return $this;
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

                $date = new \DateTime(date('r', time()+$time));
                $date->setTimezone(new \DateTimeZone('GMT'));
                $headers['Expires'] = $date->format('r');
            endif;

            // Send headers
            $headers['Cache-Control'] .= ', no-transform'; // Never transform outputed data
            $headers['Vary'] = 'Accept-Encoding';

            $this->headers($headers);

            if($file && !SYSTEM_DEBUG): // Cache file
                $this->cachetime = $time;
			 	$this->cachefile = 'output/'.Session::get('language').'/'.$file;

                if($status == self::CACHE_PROTECTED)
                    $this->cachefile .= '-'.session_id();

            endif;

            return $this;
        }

        /**
         * Redirect permanently or temporarily
         *
         * @param string    $target     Redirection URL
         * @param integer   $code       HTTP Redirection code
        **/
        public static function redirect($target, $code = 302) {
            $response = new Response;
            $response->code = $code;
            $response->headers(array('Location'=> $target));
            return $response;
        }

        /**
         * Define a view response
         * @param string    $template       View base template
         * @param integer   $status         HTTP Response code
        **/
        public static function view($template, $status = 200) {
            $response = new Response;
            $response->status($status, 'text/html; charset=utf-8');
            $response->content = function() use($response, $template) {

                // Update cache file path
                $response->cachefile .= '.html';

                // Output cached view
                if(!empty($response->cachetime) && Cache::exists($response->cachefile, $response->cachetime)
                   && Cache::time($response->cachefile) > filemtime(root(View::PATH_TEMPLATES."/$template.php"))):

                    echo Cache::get($response->cachefile);

                else: // Generate view

                    // Execute data's requests
                    if(is_closure($response->data))
                        $response->data = call_user_func($response->data);

                    $view = View::display($template, $response->data);

                    if(!is_null($response->handler))
                        $view = call_user_func($response->handler, $view, $response->data, $response->code);

                    if(!empty($response->cachetime))
                        Cache::put($response->cachefile, $view);

                    echo $view;

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
            $response->status($status, 'text/html; charset=utf-8');
            $response->content = function() use($response, $content) {

                // Update cache file path
                $response->cachefile .= '.html';

                if(!empty($response->cachetime) && Cache::exists($response->cachefile, $response->cachetime)) {

                    echo Cache::get($response->cachefile);

                }
                else {

                    // Execute data's requests
                    if(is_closure($content))
                        $content = call_user_func($content);

                    if(!empty($content))
                        $response->data = $content;

                    if(!empty($response->cachetime))
                        Cache::put($response->cachefile, $response->data);

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

                    echo Cache::get($response->cachefile);

                }
                else {

                    // Execute data's requests
                    if(is_closure($response->data))
                        $this->data = call_user_func($response->data);

                    if(!empty($data))
                        $response->data = array_merge($response->data, $data);

                    $json = json_encode($response->data);

                    if(!empty($response->cachetime))
                        Cache::put($response->cachefile, $json);

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

                     echo Cache::get($response->cachefile);

                }
                else {

                    // Execute data's requests
                    if(is_closure($response->data))
                        $response->data = call_user_func($response->data);

                    if(!empty($data))
                        $response->data = array_merge($response->data, $data);

                    $xml = xml_encode($response->data, 'document');

                    if(!empty($response->cachetime))
                        Cache::put($response->cachefile, $xml);

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

                    echo Cache::get($response->cachefile);

                }
                else {

                    if(!empty($string))
                        $response->data = $string;

                    if(is_closure($response->data))
                        $response->data = call_user_func($response->data);

                    if(!empty($response->cachetime))
                        Cache::put($response->cachefile, $response->data);

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
        public static function null($status = 204) {
            $response = new Response;
            $response->status($status, 'text/plain');
            $response->content = null;
            return $response;
        }

        /**
         * Define a custom response treatment
         * @param integer   $status
        **/
        public static function content(Closure $content, $status = 200) {
            $response = new Response;
            $response->content = function() use($content, $response) {

				if(!empty($response->cachetime) && Cache::exists($response->cachefile, $response->cachetime)) {

                    echo Cache::get($response->cachefile);

                }
                else {

					$data = call_user_func($content, $response);

                    if(!empty($response->cachetime))
                        Cache::put($response->cachefile, $data);

                    echo $data;

                }


            };
            return $response;
        }

        /**
         * Set an exception fallback
         * This callback will be called if something
         * get wrong will within content generation
         * @param   Closure     $callback       Anonymous function that will be called
        **/
        public function fallback(Closure $callback) {
			$this->fallback = $callback;
			return $this;
        }

        /**
         * Output response
         * (execute last defined response)
        **/
        public function render($module = null) {

            try {

				http_response_code($this->code);
				foreach($this->headers as $name => $value) {
					@header("$name: $value", true);
				}

                // Output content
                if(is_closure($this->content)):
                    return call_user_func($this->content, array($this));

                else:

                    if(is_closure($this->data)) // Generate data value
                        $this->data = call_user_func($this->data);

                    if(is_closure($this->handler)) // Apply a callback
                       $this->content = call_user_func($this->handler, $this->content, $this->data, $this->code);

                    return $this->content;

                endif;

            }
            catch(Exception $e) {

                if(is_closure($this->fallback))
                    $output = call_user_func($this->fallback, $e);

                if(!empty($output) && $output instanceof Response)
                    $output->render();

                else
                    throw $e;

            }

        }

    }

?>
