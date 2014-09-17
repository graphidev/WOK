<?php
    
    /**
     * This is the default controller which
     * is used to be generate default responses
    **/

    namespace Controllers;
    use \Response;

    class Root {
     
        /**
         * Call the default homepage
        **/
        public function index() {
            return Response::view('homepage', 200)
                ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PROTECTED, 'homepage');
        }
        
        /**
         * Call a static page
         * @param string    $page       The static page name/id (template name)
        **/
        public function page($name = null) {
            $path = "statics/$name";
            if(file_exists(root(PATH_TEMPLATES."/$path.php"))) {
                return Response::view($path, 200)
                    ->cache(Response::CACHETIME_MEDIUM, Response::CACHE_PROTECTED, $path);
            }
            else {
                return Response::view('404', 404);   
            }
        }
        
        /**
         * Teapot action
         * This controller generate a 418 HTTP error
        **/
        public function teapot() {
            throw new \Exception('I’m a teapot', 418);   
        }
        
    }

?>