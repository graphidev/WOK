<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Framework\Utils;

    /**
     * Manage sessions (using default PHP session features)
     *
     * @require Core/Cookie
    **/
    class Session {

        /**
         * Initialize session
         * Also define a custom session name for compatibility
        **/
        public function __construct($name = null) {

            if(!empty($name))
                session_name($name);

            session_start();

        }

        /**
         * Check if session has parameter
         *
         * @param string    $parameter
         * @param boolean   $strict
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
         * Get session informations
         *
         * @param string    $parameter
         * @param mixed     $default
         * @return mixed
        **/
        public function get($parameter, $default = false) {
            return array_value($parameter, $_SESSION, $default);
        }


        /**
         * Set session information.
         * Informations can be stored persistently thank's to cookies
         *
         * @param string    $parameter
         * @param mixed     $value
        **/
        public function set($parameter, $value) {
            array_set($parameter, $value, $_SESSION);
        }


        /**
         * Delete a session information
         *
         * @param string    $parameter
         * @return mixed
        **/
        public function delete($parameter) {
            return array_unset($parameter, $_SESSION);
        }


        /**
         * Destroy user session informations
        **/
        public function clean() {
            if(ini_get('session.use_cookies')):
                $cookie = session_get_cookie_params();
                setCookie(session_name(), '', 1, $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
            endif;

            $_SESSION = array();
            @session_destroy();
        }


    }

?>
