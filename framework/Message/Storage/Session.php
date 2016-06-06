<?php

    namespace Message\Storage;


    /**
     * Provide a sessions storage manager
    **/
    class Session /*implements \Iterator*/ {

        /**
         * Instanciate session
         * @param   string      $id         Session ID
         * @param   string      $name       Session name
        **/
        public function __construct($id = null, $name = null) {

            // Stop previous session
            if(session_id() == '' || session_status() != PHP_SESSION_NONE)
                $this->destroy();

            // Redefine session ID
            if(!empty($id))
                session_id($id);

            // Redefine Session name
            if(!empty($name))
                session_name($name);

            session_start();

        }

        /**
         * Check if session has value
         * @param   string    $parameter
         * @param   boolean   $strict
         * @return  boolean
        **/
        public function exists($parameter, $strict = false) {
            $path = &$_SESSION;
            $nodes = explode('.', $parameter);
            foreach($nodes as $i => $node) {
                if(isset($path[$node]))
                    $path = &$path[$node];
                else
                    return false;
            }
            return $strict ? !empty($path) : true;
        }


        /**
         * Alias of Session::exists
         * @param   string    $key
         * @return  mixed
        **/
        public function has($key) {
            return $this->exists($key);
        }


        /**
         * Get session informations
         * @param   string    $parameter
         * @param   mixed     $default
         * @return  mixed
        **/
        public function get($parameter, $default = false) {
            return array_value($parameter, $_SESSION, $default);
        }


        /**
         * Set session information.
         * @param   string    $parameter
         * @param   mixed     $value
        **/
        public function set($parameter, $value) {
            array_set($parameter, $value, $_SESSION);
        }


        /**
         * Delete a session information
         * @param   string    $parameter
        **/
        public function delete($parameter) {
            return array_unset($parameter, $_SESSION);
        }

        /**
         * Clear all session data
        **/
        public function flush() {
            $_SESSION = array();
        }


        /**
         * Destroy session
        **/
        public function destroy() {

            if(ini_get('session.use_cookies')) {
                $cookie = session_get_cookie_params();
                setCookie(session_name(), '', 1, $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
            }

            if (session_status() == PHP_SESSION_ACTIVE) {

                $this->flush();
                @session_destroy();

            }

        }


    }
