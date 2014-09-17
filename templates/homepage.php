<?php
    echo Parsedown::instance()->text(file_get_contents(SYSTEM_ROOT."/README.md"));

    //Markdown::defaultTransform(file_get_contents(SYSTEM_ROOT."/README.md"));
?>