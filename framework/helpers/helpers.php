<?php

    /**
     * Web Operational Kit
     * The neither huge no micro extensible framework
     *
     * @copyright   All rights reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @license     BSD <license.txt>
    **/

    /**
     * This file contains the helpers functions.
     * @package Helpers
    **/


    /**
     * Generate an XML string from an array
     *
     * @param   array     $array          Array to convert
     * @param   mixed     $xml            XML document root tag
     * @return  string                    Returns a converted XML document as a string
    **/
    function xml_encode($array, $xml = 'document'){
        if(!is_object($xml))
            $xml = new SimpleXMLElement("<$xml/>");

        foreach($array as $key => $value) {
            if(is_array($value)):
                xml_encode($value, $xml->addChild($key));
            else:
                $xml->addChild($key, $value);
            endif;
        }

        return $xml->asXML();
    }
