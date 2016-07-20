<?php

    /**
     * Web Operational Kit
     * The neither huger nor micro humble framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Command;

    /**
     * The Writer class provides an interface
     * to write into command line streams
    **/
    class Writer {

        protected $stdin;
        protected $stdout;
        protected $stderr;


        /**
         * Instanciate I/O streams
         * @param string $message       Initial message
        **/
        public function __construct($message = null) {

            $this->stdin = STDIN;
            $this->stdout = STDOUT;
            $this->stderr = STDERR;

            if(!empty($message)) {
                $lines = explode("\n", $message);
                foreach($lines as $line) {
                    $this->write($line, true);
                }
            }

        }


        /**
         * Write a message in the output stream the ask for an input
         * @param string    $message    Message to write
         * @param mixed     $default    Default value
        **/
        public function input($message,  $default = null) {

            $this->write($message, false);
            $input = trim(fgets($this->stdin));

            return (empty($input) ? $default: $input);

        }


        /**
         * Write a message in the output stream
         * @param string    $message    Message to write
        **/
        public function write($message, $newline = false) {

            if($newline)
                $message .= "".PHP_EOL."";

            fwrite($this->stdout, $message);
            return $this;
        }


        /**
         * Write a message in the error stream
         * @param string    $message    Message to write
        **/
        public function error($message) {
            fwrite($this->stderr, $message);
        }


        /**
         * Write an empty new line
        **/
        public function newline() {
            $this->write(PHP_EOL.PHP_EOL);
            return $this;
        }

        /**
         * Write a tabulation
        **/
        public function tab() {
            $this->write(chr(9));
            return $this;
        }

    }
