<?php

    /**
     * Configuration file
     * =====
     * This is the application configuration file
     * It must contains every parameters that could be
     * used by your application.
     *
     * Data must be served as array and could be used
     * and manipulated as object by the Collection class.
     *
     * /!\ Disclaimer /!\
     * In order to run some PHP specific configuration script,
     * please use the options.php file in that same directory.
    **/
    return array(


        /**
         * Environment state
         * This helps change some app behavior
        **/
        'environment'     => (object) array(
            'debug'             => false,
            'maintenance'       => false,
        ),

        'cookies'         => (object) array(
            'salt'              => 'abcd1234'
        ),

        /**
         * Application settings
        **/
        'application'      => (object) array(

            /**
             * Reconized application domains
            **/
            'domains'           => [
                'default'           => $domain = 'graphidev.fr',
                'media'             => 'api.'.$domain,
                'media'             => 'media.'.$domain,
                'assets'            => 'assets.'.$domain,
            ],

            /**
             * Accepted application locales
            **/
            'locales'     =>  [
                'fr_FR', 'en_GB',
            ],

        ),


        /**
         * Content typography settings
        **/
        'typography'    => (object) array(
            'fr_FR'         => array()
        ),


        /**
         * External services API
         *
        **/
        'services'    => (object) array(

            /* Twitter API informations */
            'twitter'      => array(
                'host'              => 'api.twitter.com',
                'consumer_key'      => '1ZHaXCk6NkLzlDlYENDeeg',
                'consumer_secret'   => 'nV9HYzLXxcjbPqWwinqsUAaizhuQLFVgGHcf018n0cI',
                'token_key'         => '263800564-8MRtHKzIYnLfHjmqHY3SonOoqeMFV5A8VmJcodkZ',
                'token_secret'      => 'fqvaJK7PyDpKE2iMlZntUK7S6ChG1D6D5geURjmkclofU'
            ),

            'jolitypo'      => (object) array(
                'rules'             => (object) array(
                    'fr_FR'             => array(
                        'Ellipsis', 'Dimension', 'Numeric', 'Dash',
                        'SmartQuotes', 'FrenchNoBreakSpace', 'NoSpaceBeforeComma',
                        'CurlyQuote', 'Hyphen', 'Trademark'
                    ),
                    'en_GB'             => array(
                        'Ellipsis', 'Dimension', 'Numeric', 'Dash',
                        'SmartQuotes', 'NoSpaceBeforeComma', 'CurlyQuote',
                        'Hyphen', 'Trademark'
                    )

                )

            )

        ),




    );
