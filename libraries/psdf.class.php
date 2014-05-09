<?php

    /**
     * PSDF : Portal Structured Data Format
     * HTML parser
     *
     * @package Libraries
    **/
    class PSDF {
        
        private static $parsers = array();        
        
        /**
         * Set a new parser model
        **/
        public  static function model($type, $callback) {
            self::$parsers[$type] = $callback;
        }
        
        /**
         * Text enrichment
         * If defined, use the parser model
        **/
        public static function richtext($input) {
            if(!empty(self::$parsers['richtext']))
                return self::$parsers['richtext']($input);
            
            /**
             * italic {abcd} <i>abcd</i> i{abcd} 
             * bold *{abcd}*
             * underscored _{abcd}_
             * striked -{abcd}-
             * exp ^{abcd}^
             * sub  \{abcd}\
             * link [text=>link(title)] [text=>link] [link]
             * link [http://graphidev.fr/a-propos "This is a link to my website"]
             * quote "abcd" / «abcd»
             * mark #{abcd}# !{abcd}!
            **/
            
            $input = preg_replace('#("|«)(.+)(»|")#isU', '<q>$2</q>', $input); // quote "abdc"
            
            $input = preg_replace('#\[(.+)=>(.+) \((.+)\)\]#isU', ' <a href="$2" title="$3">$1</a> ', $input); // lien complet)
            $input = preg_replace('#\[(.+)=>(.+)\]#isU', ' <a href="$2">$1</a> ', $input); // lien avec texte
            $input = preg_replace('#\[([a-z]{3,}://(.+))\]#isU', ' <a href="$1">$1</a> ', $input); // lien seul
    
            $input = preg_replace('# ([a-z]{3,}://(.+)) #isU', ' <a href="$1">$1</a> ', $input); // lien seul
            
            $input = preg_replace("#<i>(.+)</i>#isU", '<em>$1</em>', $input); // italic //abcd// 
            $input = preg_replace('#<b>(.+)</b>#isU', '<strong>$1</strong>', $input); // bold **abcd** 
            $input = preg_replace('#<u>(.+)</u>#isU', '<u>$1</u>', $input); // underlined
            $input = preg_replace('#<s>(.+)</s>#isU', '<del>$1</del>', $input); // striked
            
            $input = preg_replace('#{(.+)}#isU', '<mark>$1</mark>', $input); // marked {abcd}
            
            
            $input = preg_replace('#\^/(.+) #isU', '<sup>$1</sup> ', $input); // exp ^{abcd}
            $input = preg_replace('#_/(.+) #isU', '<sub>$1</sub> ', $input); // ind _{abcd}
            
            
            return nl2br($input, false);
        }
        
        /**
         * Execute a parser model
        **/
        private static function callback($type) {
            return (!empty(self::$parsers[$type]) ? self::$parsers[$type] : function() { return null; });
        }
        
        /**
         * Parse data
        **/
        public static function parse($data = array(), $parsers = array()) {
            
            /**
             * Redefine some parser models
            **/
            foreach($parsers as $type => $callback) {
                self::model($type, $callback);
            }
            
            $html = null;
            
            foreach($parsers as $type => $callback) {
                PSDF::format($type, $callback);
            }
            
            foreach($data as $i => $item) {
                if(!empty($item['type'])):
                    $parser = self::callback($item['type']);
                    $html .= $parser($item);
                endif;
            }
            
            return $html;
            
        }
        
    }


    /**
     * PSDF default parser models
    **/

    /**
     * Paragraph
    **/
    PSDF::model('paragraph', function($paragraph) {
        return '<p class="text-'.(!empty($paragraph['alignment']) ? $paragraph['alignment'] : 'default').'">'.PSDF::richtext($paragraph['value']).'</p>';
    });
    
    /**
     * Code (block)
    **/
    PSDF::model('code', function($code) {
        return '<pre><code class="language-'.$code['language'].'">'.htmlentities($code['value']).'</code></pre>';;
    });
    

    /**
     * Quote (block)
    **/
    PSDF::model('quote', function($quote) {
        if(is_array($quote['value']))
            $content = PSDF::parse($quote['value']);
        else
            $content = $quote['value'];
        
        $cite = (!empty($item['cite']) ? ' cite="'.$item['cite'].'"' : 'null');
        return "<blockquote$cite>$content</blockquote>";
    });
    
    /**
     * Separator (line)
    **/
    PSDF::model('separator', function($separator) {
        return '<hr />'; 
    });
    

    /**
     * Title
    **/
    PSDF::model('title', function($title) {
         return '<h'.$title['level'].'>'.$title['value'].'</h'.$title['level'].'>';
    });
    
    /**
     * Alert (block)
    **/
    PSDF::model('alert', function($alert) {
         return '<div class="alert '.implode(' ', $alert['classes']).'">'.$alert['value'].'</div>';
    });
    
    /**
     * List (recursive)
    **/
    PSDF::model('list', function($list) {        
        $tag = (!empty($list['ordered'])  ? 'ol' : 'ul');
        $html = "<$tag>";
        
        foreach($list['items'] as $key => $item) {
            
            if(is_array($item['value']))
               $html .= '<li>'.PSDF::parse($item['value']).'</li>';
            else
               $html .= '<li>'.$item['value'].'</li>';
        }
                    
        $html .= "</$tag>";
        return $html;
    });
    
    /**
     * Image
    **/
    PSDF::model('image', function($image) {
        if(!empty($image['legend'])):
            return '<figure class="thumbnail pull-'.$image['position'].'"><img src="'.$image['source'].'" alt="'.$image['alternative'].'" /><figcaption class="lenged text-center">'.$image['legend'].'</figcaption></figure>';
        else:
            return '<figure class="thumbnail pull-'.$image['position'].'"><img src="'.$image['source'].'" alt="'.$image['alternative'].'" /></figure>';
        endif;
    });
    
    /**
     * Form input
    **/
    PSDF::model('input', function($input) {
        switch($input['pattern']) {
            case 'text':
                $type = 'text';
                $pattern = '';
                break;
            case 'password':
                $type = 'password';
                $pattern = '';
            case 'url':
                $type = 'url';
                $pattern = '([a-z]+)://([0-9\.]+|[a-z0-9\.]+)/(.+)?';
                break;
            case 'phone':
                $type = 'tel';
                $pattern = '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$';
                break;
            case 'number':
                $type = 'number';
                $pattern = '[0-9]';
                break;
            case 'time':
                $type = 'time';
                $pattern = '[0-9]{2}:[0-9]{2}(:[0-9]{2})?';
                break;
            case 'date':
                $type = 'date';
                $pattern = '[0-9]{4}-[0-9]{2}-[0-9]{2}';
                break;
            case 'datetime':
                $type = 'datetime';
                $pattern = '[0-9]{4}-[0-9]{2}-[0-9]{2}';  
                break;
            default:
                $type = 'text';
                $pattern = '';
        } 
        
        $attributes = null;
        if(!empty($input['attributes'])):
            foreach($input['attributes'] as $name => $value) {
                $attributes .= ' '.$name.'="'.$value.'" ';
            }
        endif;
        
        return '<input type="'.$type.'" name="'.$input['name'].'" id="'.$input['name'].'" placeholder="'.$input['helper'].'" pattern="'.$pattern.'"'.($input['required'] ? ' required' : '').''.$attributes.'>';
        
    });
    
    /**
     * Form textarea
    **/
    PSDF::model('textarea', function($textarea) {
         return '<textarea name="'.$textarea['name'].'" placeholder="'.$textarea['helper'].'">'.$textarea['value'].'</textarea>';
    });
    
    /**
     * Block
    **/
    PSDF::model('block', function($block) {
        return '<div class="'.(!empty($block['classes']) ? implode(' ', $block['classes']) : '').'">'.PSDF::parse($block['items']).'</div>';
    });

?>