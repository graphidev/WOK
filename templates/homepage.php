<html>
    
    <head>
        <title>{$title}</title>
        
        {inc "inc/headers"}
        
    </head>
    
    <body>
        
        <div id="main" >
            {inc "inc/navbar"}
                        
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