<?php

        namespace Controllers\Application;

        /**
         * the Application controller class provide
         * an abstracted class that instanciate defaults
         * application controllers informations.
        **/
        class Nodes extends \Controllers\Application\Application {


            /**
             * Display the homepage
            **/
            public function home() {

                return $this->_display('default', function($data) {

                    $data['page']->title = 'Hello !';
                    $data['hello'] = 'Hello World !';

                    return $data;
                });

            }



        }
