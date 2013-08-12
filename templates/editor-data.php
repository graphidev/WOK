<?php

    /**
     * DATATYPE
     * @type
     * @value
     * @attributes
    **/
    $data = array();
    
    /**
     * PARAGRAPH
    **/
    $data[] = array(
        'type' => 'paragraph',
        'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sit amet tincidunt nisl. Donec venenatis quam nec consequat porta. Nunc quis consectetur leo, id consequat orci. Quisque vel diam elit. Vivamus volutpat pharetra lacinia. Phasellus nec sollicitudin magna. Praesent a erat ultricies mi suscipit tristique sit amet nec lacus. Nunc eget est a sem placerat congue. Etiam id lorem at nunc luctus tincidunt ut in nulla.',
        'alignement' => 'left', // left/right/justify/center
    );

    /**
     * QUOTE
    **/
    $data[] = array(
        'type' => 'quote',
        'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sit amet tincidunt nisl. Donec venenatis quam nec consequat porta. Nunc quis consectetur leo, id consequat orci.',
        'alignement' => 'left', // left/right/justify/center
        'cite' => 'name' // name or link
    );
    
    /**
     * IMAGE
    **/
    $data[] = array(
        'type' => 'image',
        //'source' => 'http://img.wok.loc/2013/08/12/generic.png',
        'source' => 'files/images/300x400.jpeg',
        'alternative' => 'toto tata', // alternative text
        'position' => 'right', // default/left/right/center
    );
    
    /**
     * AUDIO
    **/
    $data[] = array(
        'type' => 'audio',
        'source' => 'http://img.wok.loc/2013/08/12/generic.png',
        'alternative' => 'toto tata', // alternative text
        'position' => 'default', // default/left/right/center
    );

    /**
     * LIST
    **/
    $data[] = array(
        'type' => 'list',
        'items' => array(
            array('value'=>'ship 1'),
            array(
                'value' => 'ship2',
                'items' => array(
                    array('value'=>'ship 2.1'),
                    array(
                        'value'=>'ship 2.2',
                        'items' => array(
                            array('value'=>'ship 2.2.1'),
                            array('value'=>'ship 2.2.2')
                        )
                    )
                )
            ),
        ),
        'ordered' => false, // true/false
        'ships' => 'decimal' // (deprecated) decimal, roman, alpha, disc, circle, square
    );
    
    /**
     * INPUT (FORM)
    **/
    $data[] = array(
        'type' => 'input',
        'name' => 'fieldname',
        'legend' => 'text legend', // :legend or :label
        'pattern' => 'text', // text/password/email/url/phone/number/date/time/datetime/REGEX
        'helper' => 'this is an helper',
        'value' => 'this is the default value',
        'required' => true, // true/false
    ); 
    
    /**
     * TEXTAREA (FORM)
    **/
    $data[] = array(
        'type' => 'textarea',
        'name' => 'fieldname',
        'legend' => 'text legend', // :legend or :label
        'helper' => 'this is an helper',
        'value' => 'this is the default value',
        'required' => true, // true/false
    );
    
    /**
     * CHECKBOX (FORM)
    **/
    $data[] = array(
        'type' => 'checkbox',
        'name' => 'fieldname',
        'legend' => 'text legend', // :legend or :label
        'items' => array(
            array(
                'value' => 'checkbox value',
                'checked' => true,
            )
        ),
        'required' => true, // true/false
    );
    

    /**
     * RADIO (FORM)
    **/
    $data[] = array(
        'type' => 'radio',
        'name' => 'fieldname',
        'legend' => 'text legend', // :legend or :label
        'items' => array(
            array(
                'value' => 'radio value',
                'checked' => true,
            )
        ),
        'required' => true, // true/false
    );

    
    /**
     * DROPDOWN (FORM)
    **/
    $data[] = array(
        'type' => 'dropdown',
        'name' => 'fieldname',
        'legend' => 'text legend', // :legend or :label
        'items' => array(
            array(
                'value' => 'data value',
                'selected' => true,
            ),
            array( // group
                'value' => '',
                'items' => array(
                    'value' => 'data value',
                    'selected' => false,
                ), 
            )
        ),
        'required' => true, // true/false
    );


    //echo json_encode($data);

    /**
     * Datareader
     * HTML parser
    **/
    class Datareader {
        
        private static $parsers = array();        
        
        public  static function format($type, $callback) {
            self::$parsers[$type] = $callback;
        }
        
        private static function callback($type) {
            return (!empty(self::$parsers[$type]) ? self::$parsers[$type] : function() {});
        }
        
        /**
         * Parse data
        **/
        public static function parse($data = array(), $parsers = array()) {
            $html = null;
            
            foreach($parsers as $type => $callback) {
                Datareader::format($type, $callback);
            }

            foreach($data as $i => $item) {
                if(!empty($item['type'])):
                
                    $parser = self::callback($item['type']);
                
                    switch($item['type']) {
                        case 'paragraph':
                            $html .= $parser($item['value']);    
                            break;
                        case 'quote':
                            $cite = (!empty($item['cite']) ? ' cite="'.$item['cite'].'"' : 'null');
                            $html .= '<blockquote'.$cite.'>'.nl2br($item['value'], false).'</blockquote>';    
                            break;
                        case 'list':
                            $html .= $parser($item['items'], $item['ordered'], $parser);
                            break;
                        case 'image':
                            $html .= $parser($item['source'], $item['position'], $item['alternative']);
                            break;
                        case 'input':
                            $html .= $parser($item);
                            break;
                    }
                endif;
            }
            
            return $html;
            
        }
        
    }
    

    Datareader::format('paragraph', function($content) {
        return '<p>'.nl2br($content, false).'</p>';
    });

    Datareader::format('quote', function($content, $source) {
        if(is_array($content))
            $content = self::parse($content);
        $cite = (!empty($item['cite']) ? ' cite="'.$item['cite'].'"' : 'null');
        return "<blockquote$cite>$content</blockquote>";
    });

    Datareader::format('list', function($items, $ordered = false, &$parser) {
        $tag = ($ordered ? 'ol' : 'ul');
        $html = "<$tag>";
                    
        foreach($items as $key => $list) {
            $html .= '<li>'.$list['value'];
                        
            if(!empty($list['items']))
                $html .= $parser($list['items'], $ordered, $parser);
                        
            $html .= '</li>';
                            
        }
                    
        $html .= "</$tag>";
        return $html;
    });
        
    Datareader::format('image', function($source, $position, $alternative) {
        switch($position) {
            case 'left':
                $position = 'float-left';
                break;
            case 'right':
                $position = 'float-right';
                break;
            case 'center':
                $position = 'float-center';
                break;
            default:
                $position = $position;
            }
        return '<img src="'.$source.'" alt="'.$alternative.'" class="'.$position.'" />';
    });

    Datareader::format('input', function($input) {
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
        
        return '<input type="'.$type.'" name="'.$input['name'].'" id="'.$input['name'].'" placeholder="'.$input['helper'].'" pattern="'.$pattern.'"'.($input['required'] ? ' required' : '').'>';
        
    });
        
    $data[] = array(
        'type' => 'input',
        'name' => 'fieldname',
        'legend' => 'text legend', // :legend or :label
        'pattern' => 'phone', // text/password/email/url/phone/number/date/time/datetime/REGEX
        'helper' => 'this is an helper',
        'value' => 'this is the default value',
        'required' => true, // true/false
    ); 

    echo Datareader::parse($data);


?>