<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <licence.txt>
    **/

    namespace Controllers\Foundation;


    /**
     * the Http Foundation controller provides
     * an abstracted extended class that
     * instanciate both HTTP custom services
     * and default ones.
     * This class also set some services
     * shortcuts (see class properties)
    **/
    abstract class Http extends Foundation {

        /**
         * @var $request    Shortcut : $this->services->get('request')
        **/
        protected $request;

        /**
         * @var $cookies    Shortcut : $this->services->get('cookies')
        **/
        protected $cookies;

        /**
         * @var $session    Shortcut : $this->services->get('session')
        **/
        protected $session;


        /**
         * Implements additional services for the HTTP controllers
         * @param Application\Services      $services           Services collection
        **/
        public function __construct(\Application\Services $services) {

            $settings = $services->get('settings');

            // Request interface shortcut
            $this->request = $services->get('request');

            // Cookies manager
            $this->cookies = new \Message\Storage\Cookies(
                $settings->cookies->salt
            );
            $services->register('cookies', $this->cookies);

            // Session manager
            $this->session = new \Message\Storage\Session();
            $services->register('session', $this->session);


            // Instanciante default services
            parent::__construct($services);

        }


    }
