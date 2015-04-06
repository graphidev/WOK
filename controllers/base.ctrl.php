<?php

    namespace Controllers;

    use \Framework\Utils\Cookie;
    use \Framework\Utils\Session;

    /**
     * This controller define default behavior
     * and instanciate services globaly used
    **/
    class Base {

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
        public function __construct(\Framework\Core\Services $services) {

            /**
             * Register globaly used services
            **/
            $this->services = $services;
            $this->settings = $services->get('settings');

        }

    }
