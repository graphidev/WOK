<html>

    <head>
        
        <title>Handlebars</title>
        
        <?php get_library('jquery'); ?>
        <?php get_library('handlebars') ?>
        
        <script src="/wok/template/tests/handlebars/api/template_precompiled.js"></script>
        <script>
            
            $(document).ready(function() {
                //*    
                $.get('/wok/template/tests/handlebars/api/template.hb', function(source) {
                    var template = Handlebars.compile(source);
                    //var template = Handlebars.templates['template_precompiled'];
                    //alert(typeof template);
                    
                    $.get('/wok/template/tests/handlebars/api/data.php', {key:'valid'}, function(data) {
                        var json = eval('('+data+')');
                        var output = template( json );            
                        
                        setTimeout(function() {
                            
                            $('#output').html(output);
                            $('#loading').hide();
                            
                        }, 2000);
                      
                    });
                    
                });
                //*/

            });
        </script>
        
        <style>
		@import url(http://fonts.googleapis.com/css?family=Nobile:400,700);
	
		body {
			font-family: 'Nobile', sans-serif;
			color: #aaa;
			padding: 15em 2em;
		}
	
		#loading {
			margin: 0 auto 2em auto;
			text-align: center;
		}
	
		h1 {
			font-weight: 400;
			font-size: 22px;
		}
	</style>
        
    </head>
    
    <body>
        
        <div id="main">
            
            <div id="loading">
            
                <h1 id="js">Téléchargement des données</h1>
                <p><img src="http://localhost/graphidev/template/img/updating.gif" /></p>
                
            </div>
            
            <div id="output"></div>
            
	    </div>
        
        
        
    </body>
</html>
