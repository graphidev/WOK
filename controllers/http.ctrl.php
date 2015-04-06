<?php

    namespace Controllers;


    class HTTP extends Base {

        /**
         * @var $request       Request instance object
        **/
        private $request;

        /**
         * @var $cookies       Cookies tool object
        **/
        protected $cookies;

        /**
         * @var $session       Session tool object
        **/
        protected $session;

        /**
         * Get HTTP only services
        **/
        public function __construct(\Framework\Core\Services $services) {

            // Keep parent services registered
            parent::__construct($services);

            $this->request = $services->get('request');

            $this->session = $services->get('session');

            $this->cookies = $services->get('cookies', array(
                'salt key',
                $this->request->domain,
                $this->request->base,
                $this->request->secure()
            ));


            /**
             * Determine the user language
            **/
            if($this->cookies->exists('user.locale'))
                $this->locale = $this->cookies->get('user.locale');

            elseif($this->session->exists('user.locale'))
                $this->locale = $this->session->get('user.locale');

            else
                $this->locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);

            $this->locale = locale_lookup(
                $this->settings->locales,
                $this->locale,
                true,
                $this->settings->locales[0]
            );

            /**
             * Instanciate translations
            **/
            $this->translations = $services->get('locales', array($this->locale));


            /**
             * Site on maintenance : shutdown
            **/
            if($this->settings->maintenance) {
                $response = Response::view('maintenance', 503);
                exit($response->render());
            }

        }


        /**
         * Undefined controller action call
         * Prevent not found controller error
         * @param string    $function       Called function name
         * @param string    $parameters     Function arguments
        **/
        public function __call($function, $parameters) {

            return Response::view('404', 404)
                ->assign(array(
                    'title'     => $this->translation('errors->notfound.title'),
                    'message'   => $this->translation('errors->notfound.message', array('path'=>$this->request->path)),
                ))
                ->cache('404', Response::CACHETIME_MEDIUM, Response::CACHE_PUBLIC);

        }

    }
