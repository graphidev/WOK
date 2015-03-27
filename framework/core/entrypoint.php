<?php

    /**
     * Web Operational Kit
     * The neither huger no micro extensible framework
     *
     * @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    namespace Framework\Core;

    /**
     * The Entrypoint class is the main access
     * point that define global. This class defines
     * every access point informations.
     * @note this class can't be instanciated unlike it's children
     * @package Framework/EntryPoints
    **/
    abstract class EntryPoint {

        /**
         * @var string     Entry point language
        **/
        protected $language;

    }
