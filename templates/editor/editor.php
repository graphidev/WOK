<?php global $session; ?>
<html>
    
    <head>
        <title>Web Operational Kit</title>
        
        <?php Response::inc('inc/headers', PATH_TEMPLATES); ?>
        
        <script src="<?php echo path(PATH_TEMPLATES.'/js/json.stringify.js'); ?>"></script>
        <link href="<?php echo path(PATH_TEMPLATES."/css/editor.css"); ?>" rel="stylesheet" type="text/css">

        
        <script>
            $(document).ready(function() {
                /* Unuseful
                $('#content p, #content form label').on('focus',function() {
                this.designMode = 'on';
                });
                $('#content p, #content form label').on('blur',function() {
                    this.designMode = 'off';
                });
                //*/
                
                /* Bug
                $('#content form').delegate('input[type="radio"]', 'click', function(e) {
                    
                    if($(this).prop('checked')) {
                       // e.preventDefault();
                        $(this).prop('checked', false);
                    }
                    
                });
                //*/
                
                $('#content form input[type="submit"]').on('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    var name = prompt('Give a new name for this button');
                    if(name)
                        $(this).attr('value', name);
                });
                
                $('#content form').delegate('label', 'click', function(event) {
                     event.preventDefault();
                    event.stopPropagation();
                });
                
                $('#parse').on('click', function() {
                    var content = [];
                    $('#content .node').each(function(){
                        var node = {
                            'type': $(this).attr('data-item'),  
                        };
                        if(node.type == 'header') {
                            node.value = $('h1', this).text();   
                        }                         
                        if(node.type == 'paragraph') {
                            node.value = $('p', this).html();   
                        }                        
                        if(node.type == 'form') {
                            node.items = [];
                            $('form .item', this).each(function() {
                                var itemType = $(this).attr('data-type');
                                var isRequired = ($(this).attr('data-required') ? true : false);
                                
                                switch(itemType) {
                                    
                                    case 'input':
                                        var pattern = $(this).attr('data-pattern');
                                        if(pattern == 'custom')
                                            pattern = $('input', this).attr('pattern');
                                         node.items.push({
                                            'type': itemType,
                                            'pattern': pattern,
                                            'legend': $('label.legend', this).text(),
                                            'name': $('input', this).attr('name'),
                                            'default': $('input', this).val(),
                                            'helper': $('input', this).attr('placeholder'),
                                            'required': isRequired,
                                        });
                                        break;
                                        
                                    case 'textarea':
                                        node.items.push({
                                            'type': itemType,
                                            'legend': $('label.legend', this).text(),
                                            'name': $('textarea', this).attr('name'),
                                            'default': $('textarea', this).val(),
                                            'helper': $('textarea', this).attr('placeholder'),
                                            'required': isRequired,
                                        });
                                        break;
                                        
                                    case 'timepicker':
                                        node.items.push({
                                            'type': itemType,
                                            'legend': $('label.legend', this).text(),
                                            'name': $('input[type="time"]', this).attr('name'),
                                            'default': $('input[type="time"]', this).val(),
                                            'helper': $('input[type="time"]', this).attr('placeholder'),
                                            'required': isRequired,
                                        });
                                        break;
                                        
                                    case 'checkboxes':
                                        var checkboxes = [];
                                        $('.choice', this).each(function() {
                                            checkboxes.push({
                                                'label':  $('label', this).text(),
                                                'name': $('input[type="checkbox"]', this).attr('name'),
                                                'value': $('input[type="checkbox"]', this).attr('value'),
                                                'checked': $('input[type="checkbox"]', this).prop('checked'),
                                            });
                                        });
                                        node.items.push({
                                            'type': itemType,
                                            'legend': $('label.legend', this).text(),
                                            'checkboxes': checkboxes,
                                            'required': isRequired,
                                        });
                                        break;
                                    
                                    case 'radio':
                                        var radios = [];
                                        $('.choice', this).each(function() {
                                            radios.push({
                                                'label':  $('label', this).text(),
                                                'name': $('input[type="radio"]', this).attr('name'),
                                                'value': $('input[type="radio"]', this).attr('value'),
                                                'checked': $('input[type="radio"]', this).prop('checked'),
                                            });
                                        });
                                        node.items.push({
                                            'type': itemType,
                                            'legend': $('label.legend', this).text(),
                                            'radios': radios,
                                            'required': isRequired,
                                        });
                                        break;
                                        
                                    case 'dropdown':
                                        var values = [];
                                        $('select option', this).each(function() {
                                            values.push({
                                                'label':  $(this).text(),
                                                'value': $(this).attr('value'),
                                                'selected': $(this).prop('selected'),
                                            });
                                        });
                                        node.items.push({
                                            'type': itemType,
                                            'legend': $('label.legend', this).text(),
                                            'values': values,
                                            'required': isRequired,
                                        });
                                        break;
                                }                            
                                
                            });
                        }
                                                                        
                        content.push(node);
                    });
                    alert(JSON.stringify(content));
                });
                
            });
            
        </script>
    </head>
    
    <body>
        
        <section id="main" >
                        
            <article id="content" data-type="form">
                
                <figure class="item" data-item="header">
                    <h1 contenteditable="true">
                        Lorem ipsum
                    </h1>
                </figure>
                
                <figure class="item" data-item="paragraph">
                    <p contenteditable="true">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sit amet tincidunt nisl. Donec venenatis quam nec consequat porta. Nunc quis consectetur leo, id consequat orci. Quisque vel diam elit. Vivamus volutpat pharetra lacinia. Phasellus nec sollicitudin magna. Praesent a erat ultricies mi suscipit tristique sit amet nec lacus. Nunc eget est a sem placerat congue. Etiam id lorem at nunc luctus tincidunt ut in nulla.
                    </p>
                </figure>
                
                <figure class="item" data-item="list">
                    <ul>
                        <li>This is a list</li>
                        <li>This an other list</li>
                    </ul>
                </figure>
                
                <figure class="node" data-item="form">
                    <form action="#" method="get">
                        <div class="item" data-type="input" data-pattern="text">
                            <div class="instructions">
                                <div class="title" contenteditable="true">This is a title</div>
                                <div class="legend" contenteditable="true">This is a legend</div>
                            </div>
                            <div class="fields">
                                 <input type="text" name="test" value="" placeholder="this is a text input" id="form-xx-textinput-xx" />
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        
                        <div class="item" data-type="textarea">
                            <div class="instructions">
                                <div class="title" contenteditable="true">This is a title</div>
                                <div class="legend" contenteditable="true">This is a legend</div>
                            </div>
                            <div class="fields">
                                <textarea name="textarea" id="form-xx-textarea-xx" placeholder="try to type something"></textarea>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        
                        <div class="item" data-type="checkboxes">
                            <div class="instructions">
                                <div class="title" contenteditable="true">This is a title</div>
                                <div class="legend" contenteditable="true">This is a legend</div>
                            </div>
                            <div class="fields">
                                <div class="choice">
                                    <input type="checkbox" name="test" value="bidule" checked="checked" id="form-xx-checkboxes-1-1" />
                                    <label contenteditable="true" for="form-xx-checkboxes-1-1">Burger</label>
                                </div>
                                <div class="choice">
                                    <input type="checkbox" name="test" value="bidule" id="form-xx-checkboxes-1-2"/>
                                    <label contenteditable="true" for="form-xx-checkboxes-1-2">Coca</label>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        
                        <div class="item" data-type="radio">
                            <div class="instructions">
                                <div class="title" contenteditable="true">This is a title</div>
                                <div class="legend" contenteditable="true">This is a legend</div>
                            </div>
                            <div class="fields">
                                <div class="choice">
                                    <input type="radio" name="test2" value="man" checked="checked" id="form-xx-radio-1-1" />
                                    <label contenteditable="true" for="form-xx-radio-1-1">Man</label>
                                </div>
                                <div class="choice">
                                    <input type="radio" name="test2" value="woman" id="form-xx-radio-1-2"/>
                                    <label contenteditable="true" for="form-xx-radio-1-2">Woman</label>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        
                        <div class="item" data-type="timepicker">
                            <div class="instructions">
                                <div class="title" contenteditable="true">This is a title</div>
                                <div class="legend" contenteditable="true">This is a legend</div>
                            </div>
                            <div class="fields">
                                <input type="time" name="test1" value="" placeholder="this is a time input" id="form-xx-timepicker-xx" />
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        
                        <div class="item" data-type="dropdown">
                            <div class="instructions">
                                <div class="title" contenteditable="true">This is a title</div>
                                <div class="legend" contenteditable="true">This is a legend</div>
                            </div>
                            <div class="fields">
                                <select name="dropdown">
                                    <option value="R1">Réponse 1</option>
                                    <option value="R2">Réponse 2</option>
                                    <option value="R3">Réponse 3</option>
                                    <option value="R4" selected="selected">Réponse 4</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        
                        <div class="item" data-type="buttons">
                            <input type="submit" name="reset" value="reset" class="btn btn-default" />
                            <input type="submit" name="submit" value="submit" class="btn btn-inverse" />
                        </div>
                    </form>
                </figure>
                
                <button type="button" id="parse" class="btn btn-primary btn-block btn-large"><?php echo _e('editor:submit'); ?></button>
                
            </article>
           
            
        </section>
    
    </body>
    

</html>