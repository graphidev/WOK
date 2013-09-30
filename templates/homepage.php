<html>
    
    <head>
        <title>{$title}</title>
        
        {zone "inc/headers"}
        
    </head>
    
    <body>
        
        <div id="main" >
                        
            <div class="content">
                {*
                {if $config}        
                    <p>This is a loop ...</p>
                    {loop $config.urls}
                        <p>{$key} => {$value}</p>
                    {/loop}
                {/if}
                *}
                <p>This is a jump</p>
                {jump 1990 => 2015 / 5}
                    <p>{$step}</p>
                {/jump}
                                
                
                
            </div>
           
            
        </div>
    
    </body>
    

</html>