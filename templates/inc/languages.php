<div id="languages" class="pull-right">
    <?php
        $languages = explode(',', SYSTEM_ACCEPT_LANGUAGES);
        foreach($languages as $i => $language) {
            echo '<a href="?_lang='.$language.'" title="'._e("languages:$language").'"><img src="'.path(PATH_RESOURCES."/images/flags/$language.png").'" alt="'.$language.'" /></a>';
        }
    ?>
</div>
<div class="clearfix"></div>