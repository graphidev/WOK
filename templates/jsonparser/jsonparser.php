<html manifest="<?php echo path('/jsonparser/manifest'); ?>">
    
    <head>
        <title>JSON Parser</title>
          
        <script src="<?php echo path('jsonparser/jquery'); ?>"></script>
        <link href="<?php echo path(PATH_TEMPLATES.'/jsonparser/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css">
        <script src="<?php echo path(PATH_TEMPLATES.'/jsonparser/bootstrap/js/bootstrap.min.js'); ?>"></script>
        
        <style>
            #main {
                width: 800px;
                margin: 40px auto;
            }
            
            #analyse {
                display:none;
            }
            
            #get textarea {
                height: 250px;
                -moz-border-radius: 5px 5px 0 0;
                -wekbit-border-radius: 5px 5px 0 0;
                border-radius: 5px 5px 0 0;
            }
            #get #load {
                -moz-border-radius: 0 0 5px 5px;
                -wekbit-border-radius: 0 0 5px 5px;
                border-radius: 0 0 5px 5px;
            }
            
            #analyse #explorer {
                padding: 20px 0;
            }
            
            #analyse #explorer .node .extends, 
            #analyse #explorer .items .add {
                cursor: pointer;
            }
            
            #analyse #explorer .items .name {
                cursor: pointer;
            }
            
            #analyse #explorer .item .delete {
                visibility: hidden;
                cursor: pointer;
                color:#aaa;
            }
            
            #analyse #explorer .item:hover > .delete,
            #analyse #explorer .node:hover > .delete, {
                visibility: visible;
            }
            
            #analyse #explorer .node .item {
                padding-left: 50px;
                margin: 10px 0;
            }
        </style>
        
        <script>
            //{"menu":{"home":"Accueil","about":"À propos"},"token":{"authorized":"Token autorisé","unauthorized":"Token non autorisé !"},"footer":{"plan":"Plan du site","notices":"Mentions légales","terms":"Conditions générales de ventes","credits":"Tous droits réservés - :year - :owner","test":{}}}
            
            var json = [];
            
            function generate_html(data, level) {
                
                for(var line in data) {
                    if(typeof data[line] == 'object') {
                        
                        $('#explorer .node .items[data-level="'+(!level ? 'zero':level)+'"]').append('<div class="item"><div class="node"><span class="label extends" data-open="'+(!level ? '': level+'.')+line+'">'+line+' :</span> <span class="delete">&times;</span> <div class="items" data-level="'+(!level ? '': level+'.')+line+'"></div></div></div>');
                        
                    } else {
                        
                        switch(typeof data[line]) {
                            case 'string':
                                var classLabel='label-success';
                            break;
                            case 'number':
                                var classLabel="label-warning";
                            break;
                            default:
                                var classLabel="label-danger";
                        }
                        
                        $('#explorer .items[data-level="'+(!level ? 'zero':level)+'"]').append('<div class="item"><span class="label '+classLabel+' name" data-name="'+line+'">'+line+' :</span> <span class="value">'+data[line]+'</span> <span class="delete">&times;</span></div>')
                        
                    }
                }
            }
            
            // http://localhost/wok/languages/fr_FR/default.json
            $(document).ready(function() {
                
                // DOWNLOAD FROM URL
                $('#download').on('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $('#download').text('Loading...');
                    $.get($('#url').val(), function(data) {
                        json = data;
                        
                        var level = 'zero';
                        $('#explorer .items[data-level="'+level+'"]').append('<div class="item"><span class="label label-info add node" data-target="'+level+'">+ Node</span> <span class="label label-info add data" data-target="'+level+'">+ Data</span></div>');
                        generate_html(json);
                        
                        $('#download').text('Go !');
                        $('#get').slideUp();
                        $('#analyse').slideDown();
                    })
                });
                
                // LOAD FROM TEXTAREA
                $('#load').on('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    json = eval('('+$('#input').val()+')');
                                        
                    var level = 'zero';
                    $('#explorer .items[data-level="'+level+'"]').append('<div class="item"><span class="label label-info add node" data-target="'+level+'">+ Node</span> <span class="label label-info add data" data-target="'+level+'">+ Data</span></div>');
                    generate_html(json);
                                        
                    $('#get').slideUp();
                    $('#analyse').slideDown();
                });
                
                
                // EXTENDS
                $('#explorer').delegate('.node .extends', 'click', function() {
                    var level = $(this).attr('data-open');
                    var index = level.split('.');
                    if(index.length > 1) {
                        eval('var data = json.'+index.join('.')+'');
                    } else {
                        eval('var data = json.'+level+'');  
                    }
                    
                    if($(this).hasClass('opened')) {
                        var parent = $(this).parent('.node')
                        var childs = $('.items[data-level="'+level+'"]', parent);
                        if($(childs).hasClass('hidden'))
                            $(childs).removeClass('hidden');
                        else
                            $(childs).addClass('hidden');
                        
                    } else {
                        $(this).addClass('opened');
                        $('#explorer .items[data-level="'+level+'"]').append('<div class="item"><span class="label label-info add node" data-target="'+level+'">+ Node</span> <span class="label label-info add data" data-target="'+level+'">+ Data</span></div>');
                        generate_html(data, level);
                    }
                });
                
                
                // ADD NODE/ENTRY
                $('#explorer .items').delegate('.add', 'click', function() {
                    var target = $(this).attr('data-target');
                    var index = target.split('.');                    
                    
                    if($(this).hasClass('node')) {
                        var name = prompt('Please tell us what\'s the object name');
                        if(name) {
                           $('#explorer .items[data-level="'+target+'"]').append('<div class="item"><div class="node"><span class="label extends" data-open="'+(target == 'zero' ? name : target+'.'+name)+'">'+name+' :</span> <div class="items" data-level="'+(target == 'zero' ? name : target+'.'+name)+'"></div></div></div>');
                            $('#explore .extends[data-open="'+(target == 'zero' ? name : target+'.'+name)+'"]').click();
                            
                            if(index.length > 1) 
                                eval('json.'+index.join('.')+'.'+name+' = {};'); 
                            else
                                json[name] = {};
                        }
                        
                        
                    } else if($(this).hasClass('data')) {
                        var name = prompt('Please tell us what\'s the variable name');
                        var value = prompt('Please give a value for this item : '+name);
                        if(name && value) {
                           $('#explorer .items[data-level="'+target+'"]').append('<div class="item"><span class="label label-success name" data-name="'+name+'">'+name+' :</span> <span class="value">'+value+'</span> <span class="delete">&times;</span></div>');
                            if(index.length > 1) 
                                eval('json.'+index.join('.')+'.'+name+' = "'+value+'";'); 
                            else
                                json[name] = value;
                        }
                        
                    }
                                        
                });
                
                // EDIT VALUE
                $('#explorer .item').on('click', function(){
                    var name = $('.name', this).attr('data-name');
                    var value = prompt('Type the new value : '+name);
                    var target = $(this).parent('.items').attr('data-level');
                    $(this).text(value);
                    if(value) {
                        eval('json.'+target+'.'+name+' = "'+value+'";');
                    }
                });
                
                // DELETE NODE/ENTRY
                $('#explorer .items .delete').on('click', function() {
                    var target = $(this).parent('.item');
                    $(target).remove();
                });
                
                // PARSE
                JSON.stringify = JSON.stringify || function (obj) {
                    var t = typeof (obj);
                    if (t != "object" || obj === null) {
                        // simple data type
                        if (t == "string") obj = '"'+obj+'"';
                        return String(obj);
                    }
                    else {
                        // recurse array or object
                        var n, v, json = [], arr = (obj && obj.constructor == Array);
                        for (n in obj) {
                            v = obj[n]; t = typeof(v);
                            if (t == "string") v = '"'+v+'"';
                            else if (t == "object" && v !== null) v = JSON.stringify(v);
                            json.push((arr ? "" : '"' + n + '":') + String(v));
                        }
                        return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
                    }
                };
                    
                $('#parse').on('click', function() {
                    $('#input').val(JSON.stringify(json));
                    $('#get').slideDown();
                    $('#analyse').slideUp();
                    $('#analyse .items[data-level="zero"]').html('');
                });
                
            });
        </script>
        
    </head>
    
    <body>
        
        <section id="main">
            
            <h1>JSON Parser</h1>
            
            <figure id="get">
                
                <form action="#" method="post">
                    <div class="input-group">
                        <input type="text" class="form-control" id="url" name="url" type="text" placeholder="Paste the JSON file URL ...">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" data-loading-text="Loading..." id="download">Go!</button>
                        </span>
                    </div>
                </form>
                
                <p class="text-center text-info">-- OR --</p>
                
                <form action="#" method="post">
                    <textarea class="form-control" rows="3" id="input" placeholder="Paste your JSON code ..."></textarea>
                    
                    <button type="button" class="btn btn-default btn-block" id="load">Analyse</button>
                </form>
                
            </figure>
            
            <article id="analyse">
                
                <div id="explorer"><div class="node" data-level="zero"><div class="items" data-level="zero"></div></div></div>
                
                <button type="button" id="parse" class="btn btn-block btn-default">Parse</button>
                
            </article>
            
        </section>
    
    </body>
    

</html>