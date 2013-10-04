<html>
    
    <head>
        <title>Test for template format</title>
        
        <?php $headers(); ?>
        
    </head>
    
    <body>
        
        <div id="main" >                        
            <div class="content">
                <p>{$data.string}</p>
                
                {noparse}
                    Are you a {$do.not.parse.this} ? 
                {/noparse}
                
                <ul>
                    {loop $data.array}
                    <li>{$key} => {$value}</li>
                    {/loop}
                </ul>
                
                <p>
                    jump :
                    <select name="year">
                        <!-- 
                            ([0-9]|[\+-\*\/\(\)%\^]+)
                            +/-/*/\/
                            / 2 
                            / 2%
                            / (i2%)
                        -->
                        {jump 1990 => 2013}
                            <option value="{$step}">{$step}</option>
                        {/jump}
                    </select>
                </p>
                
                
                {zone "tests-zone"}
                
            </div>
           
            
        </div>
    
    </body>
    

</html>