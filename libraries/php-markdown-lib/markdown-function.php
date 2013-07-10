<?php
    
    use \Michelf\Markdown;

    function markdown($string) {
        
        return Markdown::defaultTransform($string);
    }
    
?>