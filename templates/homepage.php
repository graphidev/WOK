<html>
    
    <head>
        <title>{$page.title}</title>
        
        {zone "inc/headers"}
        
    </head>
    
    <body>
        
        <div id="main" >                        
            <div class="content">
                    
                <h1>This is a loop ...</h1>
                {loop $session.account}
                    <p>{$key} => {$value}</p>
                {/loop}
                
                <p>
                    jump :
                    <select name="year">
                        {jump 1990 => 2015 / 1}
                            <option value="{$step}">{$step}</option>
                        {/jump}
                    </select>
                </p>
               
                
                
                <p>Variable : {$page.title}</p>
                
                <p>Locale : {@default:test $locales} / <?php echo _e('timezones:Africa:Africa'); ?></p>
                
                
            </div>
           
            
        </div>
    
    </body>
    

</html>