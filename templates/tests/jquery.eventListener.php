<html>
    
    <head>
        <title>WOK tests | Web Operational Kit</title>
        
        <?php tpl_headers(); ?>
        
        <?php get_library('jquery'); ?>
        
        <script>
            (function ( $ ) {
                
                var settings = {
                    interval: 100,
                    unlimited: false,
                    strict: false
                }
                
                var methods = []
                methods['remove'] = function(element) {
                    if($(element).length < 1) return true;
                }
                
                $.fn.listen = function(event, callback, parameters) {
                    var events = event.split(' ');
                    var options = $.extend({}, settings, parameters);
                
                    for(i=0; i < events.length; i++) {
                          var interval = setInterval(function(){
                              if(events[i] == 'remove') {
                                  var isTrue = methods[events](this);                                
                              }
                              
                              if(isTrue) {
                                  callback(this);
                                  if(!options.unlimited)
                                      clearInterval(interval);
                              }
                          }, 100);               
                        
                    }
                                        
                    return this;
                }
                
            }(jQuery));
        </script>
        
        <script>
            $(document).ready(function(){
                $('.content').html('<p>Document loaded</p>');
                
                $('p').listen('remove', function(){
                    alert("P are deads."); 
                });
            });
        </script>
        
    </head>
    
    <body>
        
        <div id="main">
            
            <?php tpl_banner(); ?>
            
            <div class="content">
                
                
                
            </div>
            
            <button type="button" onclick="$('p').remove();">Remove all P tags</button>
            
        </div>
    
    </body>
    

</html>