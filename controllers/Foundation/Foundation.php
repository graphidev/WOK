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
     * the Foundation controller provide
     * an abstracted class that instanciate defaults
     * controllers informations.
    **/
    abstract class Foundation {

        protected $services;
        protected $settings;

        /**
         * Instanciate services informations
         * @param Services      $services       Application service
        **/
        public function __construct(\Application\Services $services) {

            // Register application services
            $this->services = $services;
            $this->settings = $services->get('settings');

        }


    }
