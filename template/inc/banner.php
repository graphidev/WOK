<div class="navbar ">
    <div class="navbar-inner">
        <a class="brand" href="<?php echo path('homepage'); ?>">WOK</a>
        <ul class="nav">
            
            <li class="divider-vertical"></li>
            
            <li class="<?php if(get_request() == 'about') echo 'active'; ?>"><a href="<?php echo path('about'); ?>">About</a></li>
            
            <li class="divider-vertical"></li>
            
            <li class="<?php if(get_request() == 'start') echo 'active'; ?>"><a href="<?php echo path('start'); ?>">Basic usage</a></li>
            
            <li class="divider-vertical"></li>
            
            <li class="<?php if(get_request() == 'tools') echo 'active'; ?>"><a href="<?php echo path('tools'); ?>" class="dropdown">Included tools</a></li>
            
            <li class="divider-vertical"></li>
            
            <li class="<?php if(get_request() == 'templates') echo 'active'; ?>"><a href="<?php echo path('templates'); ?>">Template tools</a></li>
            
            <li class="divider-vertical"></li>
            
            <li class="<?php if(preg_match('#^libraries(.+)?#', get_request())) echo 'active'; ?>"><a href="<?php echo path('libraries'); ?>">External libraries</a></li>
            
            <li class="divider-vertical"></li>
            
        </ul>
        
        <ul class="nav pull-right">
            <li><a href="https://github.com/graphidev/WOK">On Github</a></li>
        </ul>
    </div>
</div>