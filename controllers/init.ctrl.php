<?php

    namespace Controllers;

    /**
     * This controller define default behavior
     * and instanciate services globaly used
    **/
    class Init {

        /**
         * @var  $services      Services collection
        **/
        protected $services;

        /**
         * @var $settings       Application settings service
        **/
        protected $settings;

        /**
         * @var $translations   Application locales service
        **/
        protected $translations;

        /**
         * @var $locale         User locale (e.g: en_GB)
        **/
        protected $locale;


        /**
         * Initialize services that will be used in every custom controller
         * @note This method will be called by the framework instance
         * @param Object    $services       Services collection
        **/
        public function __contruct(\Framework\Core\Services $services) {

            /**
             * Register globaly used services
            **/
            $this->services = $services;
            $this->settings = $services->get('application->settings');


            /**
             * Determine the user language
            **/
            if(Cookie::exists('user.language'))
                $this->locale = Cookie::get('user.language');

            elseif(Session::exists('user.language'))
                $this->locale = Session::get('user.language');

            else
                $this->locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);

            $this->locale = locale_lookup(
                $this->settings->language,
                $this->locale,
                true,
                array_shift($this->settings->language)
            );


            /**
             * Instanciate translations
            **/
            $this->translations = $services->get('locales', array($this->locale));


            /**
             * Site on maintenance : shutdown
            **/
            if($this->settings->maintenance) {
                $response = Response::view('maintenance', 503)

                /*
                if(is_a($this->translations, 'Framework\Services\Locales', true))
                    $response->assign(array(

                    ));
                */
                
                exit($response->render());
            }

        }


        /**
         * Undefined controller action
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
