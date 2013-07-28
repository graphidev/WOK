<html>
    
    <head>
        <title>Session class | Web Operational Kit</title>
        
        <?php tpl_headers(); ?>
        
        <script>
            $(document).ready(function() {
                
                var tool = null;
                $('.nav.nav-list li a').on('click', function() {
                    $('.nav.nav-list li').removeClass('active');
                    $(this).parent('li').addClass('active');
                    tool = $(this).attr('href');
                });
                
                $('.nav.nav-tabs li a').on('click', function(event) {
                    event.stopPropagation();
                    //event.prependDefault();
                    
                    var tool = $(this).parent('li').parent('ul').attr('data-tool');
                    var open = $(this).attr('data-open');
                    
                    $('#'+tool+' .nav.nav-tabs li').removeClass('active');
                    $(this).parent('li').addClass('active');
                    
                    $('#'+tool+' .tab').addClass('hidden');
                    $('#'+tool+' .tab.'+open).removeClass('hidden');
                    
                    return false;
                });
                
            });
        </script>
        
        <style>
            .nav.nav-tabs li.default {
                font-weight: bold;   
            }
        </style>
        
    </head>
    
    <body>
        
        <div id="main">
            
            <?php tpl_banner(); ?>
            
            <ul class="breadcrumb">
                <li><a href="<?php echo path(); ?>">WOK</a> <span class="divider">/</span></li>
                <li><a href="<?php echo path('tools/'); ?>">Included tools</a> <span class="divider">/</span></li>
                <li>Session class</li>
            </ul>
            
            <div class="row">
            
                <div class="span3">
                    <div class="well" style="padding:0;">
                        <ul class="nav nav-list">
                            <li class="nav-header">Session class</li>
                            <li class="active"><a href="#introduction">Introduction</a></li>
                            
                            <li class="nav-header">Get visitor settings</li>
                            <li><a href="#construct">construct</a></li>
                            <li><a href="#upload">ip</a></li>
                            <li><a href="#download">language</a></li>
                            <li><a href="#select">browser</a></li>
                            
                            <li class="nav-header">Treatments methods</li>
                            <li><a href="#move">move</a></li>
                            <li><a href="#archive">archive</a></li>
                            <li><a href="#resize">resize</a></li>
                            
                            <li class="nav-header">Others methods</li>
                            <li><a href="#info">info</a></li>
                            <li><a href="#reset">reset</a></li>
                        </ul>
                    </div>
                    
                </div>
                
                <div class="span7">
                    
                    <div id="introdution">
                        <h1>Session class</h1>
                        
                        <p>
                            As any web project is made to be viewed by users, the session class will help you to define and get some informations about your visitor and give him a better visit experience.
                        </p>
                        
                        <div class="alert">This class requires an up to date <b>browscap.ini</b> file.</div>
                                                   
                </div>
                
                
           
            
        </div>
    
    </body>
    

</html>