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
        'value' => 'Lorem ipsum dolor <i>sit amet</i>, consectetur <u>adipiscing elit</u>. Aliquam sit <s>amet tincidunt nisl</s>. Donec venenatis quam nec consequat porta. Nunc quis <b>consectetur leo</b>, id consequat orci. "Quisque vel diam elit". Vivamus volutpat pharetra lacinia. Phasellus nec sollicitudin magna. Praesent a erat [ultricies=>http://example.iana.org/ (texte alternatif)] mi suscipit tristique sit http://www.graphidev.fr amet nec lacus. Nunc {eget est^/123 a sem placerat} congue. Etia^/id lorem_/at nunc luctus tincidunt ut in nulla.',
        'alignment' => 'left', // left/right/justify/center
    );

    /**
     * QUOTE
    **/
    $data[] = array(
        'type' => 'quote',
        'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sit amet tincidunt nisl. Donec venenatis quam nec consequat porta. Nunc quis consectetur leo, id consequat orci.',
        'alignment' => 'left', // left/right/justify/center
        'cite' => 'name' // name or link
    );

    /**
     * QUOTE
    **/
    $data[] = array(
        'type' => 'code',
        'value' => '<?php echo "Hello World !" ?>',
        'language' => 'PHP',
    );

    /**
     * ALERT
    **/
    $data[] = array(
        'type' => 'alert',
        'style' => 'default',
        'value' => 'This is an alert',
    );
    
    /**
     * TITLE
    **/

    $data[] = array(
        'type' => 'title',
        'level' => 1,
        'value' => 'This is a title',
    );

    /**
     * IMAGE
    **/
    $data[] = array(
        'type' => 'image',
        //'source' => 'http://img.wok.loc/2013/08/12/generic.png',
        'source' => 'files/images/300x400.jpeg',
        'alternative' => 'toto tata', // alternative text
        'legend' => 'This is a nice picture',
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
     * VIDEO
    **/
    $data[] = array(
        'type' => 'video',
        'source' => 'http://www.youtube.com/watch?v=S2iQDfVsVDM',
        'alternative' => 'toto tata', // alternative text
        'position' => 'default', // default/left/right/center
    );

    /**
     * LIST
    **/
    $data[] = array(
        'type' => 'list',
        'ordered' => false, // true/false
        'ships' => 'decimal', // (deprecated) decimal, roman, alpha, disc, circle, square
        'items' => array(
            array('value'=>'ship 1'),
            array(
                'value'=> 'ship 2',
                'items' => array(
                    array('value'=>'ship 2.1'),
                    array(
                        'value'=>'ship 2.2',
                        'items' => array(
                            array('value'=>'ship 2.2.1'),
                            array('value'=>'ship 2.2.2'),
                        )
                    ),
                ),
            ),
        ),
    );

    /**
     * SEPARATOR
    **/
    $data[] = array(
        'type' => 'separator',
        'attributes' => array()
    );
    
    /**
     * INPUT (FORM)
    **/
    $data[] = array(
        'type' => 'input',
        'name' => 'fieldname',
        'legend' => 'This is a number input', // :legend or :label
        'pattern' => 'number', // text/password/email/url/phone/number/date/time/datetime/REGEX
        'attributes' => array(
           'min' => 0,
            'max' => 25,
            'step' => 1, 
        ),
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
        'pattern' => '',
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

    $data[] = array(
        'type' => 'input',
        'name' => 'fieldname',
        'legend' => 'text legend', // :legend or :label
        'pattern' => 'phone', // text/password/email/url/phone/number/date/time/datetime/REGEX
        'helper' => 'this is a phone field',
        'value' => 'this is the default value',
        'required' => true, // true/false
    ); 


    //header('Content-type: application/json'); echo json_encode($data); exit();

    

    

    $block = array(
        array(
            'type' => 'block',
            'items' => $data,
        )
    );
    
    echo PSDF::parse($block);
    

?>