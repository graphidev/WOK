<!doctype html>
<html lang="<?= $page->lang ?>">

    <head>

        <?php $view->zone('inc/meta', [
            'title'     => 'Welcome home !',
            'styles'    => ['form'],
            'scripts'   => ['form'],
        ]); ?>

    </head>

    <body>

        <div class="site">

            <?php $view->zone('inc/sidebar'); ?>

            <div class="site-main">

                <form action="<?= $view->permalink('Application\Nodes->form'); ?>" method="post">


                    <div class="field" field-state="<?= $form->text->state; ?>">

                        <label for="field-label">Champ texte</label>

                        <div class="field-value" field-required>
                            <input type="text" name="form[text]" value="<?= $form->text->value; ?>" minlength="2" maxlength="10" placeholder="Do the fuck what you want" required>
                        </div>

                        <div class="field-validation">
                            <span field-validation="valid">
                                <?= $view->i18n('text.valid', [], 'form'); ?>
                            </span>
                            <span field-validation="required">
                                <?= $view->i18n('text.invalid.required', [], 'form'); ?>
                            </span>
                            <span field-validation="minlength">
                                <?= $view->i18n('text.invalid.minlength', [], 'form'); ?>
                            </span>
                            <span field-validation="minrange">
                                <?= $view->i18n('text.invalid.minrange', [], 'form'); ?>
                            </span>
                            <span field-validation="maxrange">
                                <?= $view->i18n('text.invalid.maxrange', [], 'form'); ?>
                            </span>
                            <span field-validation="step">
                                <?= $view->i18n('text.invalid.step', [], 'form'); ?>
                            </span>
                        </div>

                        <div class="field-helper">
                            <?= $view->i18n('text.helper', [], 'form'); ?>
                        </div>

                    </div>


                    <button type="submit">
                        Envoyer
                    </button>


                </form>

            </div>

        </div>

    </body>

</html>
