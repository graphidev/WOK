<!doctype html>
<html lang="<?= $page->lang ?>">

    <head>

        <?php $view->zone('inc/headers', [
            'title'     => 'Welcome home !',
            'styles'    => ['homepage'],
            //'scripts'   => [],
        ]); ?>

        <link rel="styleesheet" href="<?= $view->asset('style','default.css'); ?>">
        <script src="<?= $view->asset('script', 'default.js'); ?>"></script>

    </head>

    <body>

        <div class="site">

            <?php $view->zone('inc/sidebar'); ?>

            <div class="site-main">

                <?php /* $view->zone('alerts', $alerts); */ ?>

                <?php $view->zone('inc/navigation'); ?>

                <div class="site-body">
                    <?php /* ?>
                    <a href="<?= $permalinks->home ?>"><?= $view->i18n('default', 'navigation.home'); ?></a>
                    <a href="<?= $permalinks->about ?>"><?= $view->i18n('default', 'navigation.about'); ?></a>
                    <a href="<?= $permalinks->journal ?>"><?= $view->i18n('default', 'navigation.journal'); ?></a>
                    <a href="<?= $permalinks->contact ?>"><?= $view->i18n('default', 'navigation.contact'); ?></a>
                    */ ?>
                    <?= $view->escape($hello); ?>

                </div>

                <?php $view->zone('inc/footer'); ?>

            </div>

        </div>

    </body>

</html>
