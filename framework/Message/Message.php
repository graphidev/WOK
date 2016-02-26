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

    use \Message\Components\Uri;
    use \Message\Components\Stream;
    use \Message\Components\Headers;

    abstract class Message {

        protected $headers;
        protected $body;

        /**
         * Instanciate message interface
         * @param   Stream      $body           Message body
         * @param   Headers     $headers        Message headers
        **/
        public function __construct(Stream $body, Headers $headers) {
            $this->headers = $headers;
            $this->body    = $body;
        }

        protected function getHeaders() {
            return $this->headers;
        }

        protected function getBody() {
            return $this->body;
        }


    }
