<?php

    /**
    * Web Operational Kit
    * The neither huger nor micro humble framework
    *
    * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
    * @license     BSD <license.txt>
    **/

    namespace Message\Components;

    /**
     * The Stream class provide an interface
     * for both HTTP request and response body
    **/
    class Stream {


        protected $stream;
        protected $meta;

        protected $seekable = false;
        protected $readable = false;
        protected $writable = false;

        /**
         * Instanciate Stream object
         * @param   resource        $stream         Stream
        **/
        public function __construct($stream) {

            if(!is_resource($stream))
                throw new \Domain('The '.__CLASS__.' interface requiert a stream');

            $this->stream = $stream;

            $this->meta     = stream_get_meta_data($this->stream);
            $this->seekable = $this->meta['seekable'];

            $mode = str_replace(['b','t'], '', $this->meta['mode']);
            $this->readable = in_array($mode, ['r', 'r+', 'w+', 'a+', 'x+', 'c+']);
            $this->writable = in_array($mode, ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+']);

        }


        /**
         * Get the stream itself
         * @return stream
        **/
        public function getStream() {
            return $this->stream;
        }


        /**
         * Is the stream readable
         * @return bool
        **/
        public function isReadable() {
            return $this->readable;
        }


        /**
         * Is the stream writable
         * @return bool
        **/
        public function isWritable() {
            $this->writable;
        }

        /**
         * Is the stream seekable
         * @return bool
        **/
        public function isSeekable() {
            return $this->seekable;
        }


        /**
         * Get the stream size
        **/
        public function getSize($default = null) {
            $stats = fstat($this->stream);
            return isset($stats['size']) ? $stats['size'] : $default;
        }


        /**
         * Get the stream meta data
         * @return array
        **/
        public function getMetaData() {
            return $this->meta;
        }


        /**
         * Get the stream content
        **/
        public function getContents() {

            if (!$this->isReadable() || ($contents = stream_get_contents($this->stream)) === false) {
                throw new \RuntimeException('Could not get contents of stream');
            }

            rewind($this->stream);

            return $contents;

        }


        /**
         * Shortcut for getContents() method
        **/
        public function __toString() {
            return $this->getContents();
        }


        /**
         * Seek to the beginning of the stream;
        **/
        public function rewind(){
            if (!$this->isSeekable() || rewind($this->stream) === false) {
                throw new \RuntimeException('Could not rewind stream');
            }
        }


        /**
         * Read the stream
         * @param integer       $length         Reading length bytes
        **/
        public function read($length = null) {

            if(is_null($length))
                $length = $this->getSize(0);

            if (!$this->isReadable() || ($data = fread($this->stream, $length)) === false) {
                throw new \RuntimeException('Could not read from stream');
            }
            return $data;
        }


        /**
         * Write in the stream
        **/
        public function write($string) {

            if (!$this->isWritable() || ($written = fwrite($this->stream, $string)) === false) {
                throw new \RuntimeException('Could not write to stream');
            }
            return $written;
        }


    }
