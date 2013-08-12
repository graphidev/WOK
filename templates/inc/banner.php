<div class="navbar ">
    <div class="navbar-inner">
        <a class="brand" href="<?php echo path('homepage'); ?>"><?php echo _e('project.name'); ?></a>
        <ul class="nav">
            
            <li class="divider-vertical"></li>
            
            <li class="<?php if(get_request() == 'about') echo 'active'; ?>"><a href="<?php echo path('about'); ?>"><?php echo _e('navigation.about'); ?></a></li>
            
            <li class="divider-vertical"></li>
            
            <li class="<?php if(get_request() == 'start') echo 'active'; ?>"><a href="<?php echo path('start'); ?>"><?php echo _e('navigation.use'); ?></a></li>
            
            <li class="divider-vertical"></li>
            
            <li class="<?php if(preg_match('#^tools/(.+)?$#', get_request())) echo 'active'; ?>"><a href="<?php echo path('tools/'); ?>" class="dropdown"><?php echo _e('navigation.tools'); ?></a></li>
            
            <li class="divider-vertical"></li>
            
            <li class="<?php if(get_request() == 'templates') echo 'active'; ?>"><a href="<?php echo path('templates'); ?>"><?php echo _e('navigation.scripts'); ?></a></li>
            
            <li class="divider-vertical"></li>
            
            <li class="<?php if(preg_match('#^libraries(.+)?$#', get_request())) echo 'active'; ?>"><a href="<?php echo path('libraries'); ?>"><?php echo _e('navigation.libraries'); ?></a></li>
            
            <li class="divider-vertical"></li>
            
        </ul>
        
        <ul class="nav pull-right">
            <li><a href="https://github.com/graphidev/WOK"><?php echo _e('navigation.github'); ?></a></li>
        </ul>
    </div>
</div>