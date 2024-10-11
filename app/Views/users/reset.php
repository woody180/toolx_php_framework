<?php $this->layout('partials/template', ['title' => $title]) ?>

<?= $this->start('mainSection') ?>

<section class="uk-section">
    <div class="uk-container uk-container-small">
        
        <div class="uk-card uk-card-default">
            <div class="uk-card-body">
                <h1><?= lang('auth.restore_account') ?></h1>
                
                <?php if (hasFlashData('message')): ?>
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p><?= getFlashData('message'); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (hasFlashData('error')): ?>
                <div class="uk-alert-danger" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p><?= getFlashData('error'); ?></p>
                </div>
                <?php endif; ?>
                
                <form id="alter-login-form" class="alter-login-form uk-grid-medium uk-child-width-1-1k" uk-grid action="<?= baseUrl("users/reset") ?>" method="POST" accept-charset="utf-8">
                    <?= csrf_field() ?>
                    <div>
                        <label for="email" class="uk-form-label"><?= lang('auth.existing_email') ?></label>
                        <input id="email" type="email" name="email" class="uk-input" value="<?= getForm('email') ?>">
                        <p class="uk-margin-remove uk-text-danger uk-text-small"><?= implode(', ', getFlashData('errors')->email ?? []) ?></p>
                    </div>
                   
                    <div>
                        <label for="password" class="uk-form-label"><?= lang('auth.new_password') ?></label>
                        <input id="password" type="password" name="password" class="uk-input" value="">
                        <p class="uk-margin-remove uk-text-danger uk-text-small"><?= implode(', ', getFlashData('errors')->password ?? []) ?></p>
                    </div>
                    
                    <div id="account-buttons-set" class="uk-flex uk-flex-between uk-flex-middle">
                        <button class="uk-button uk-button-primary" type="submit"><?= lang('auth.restore_password') ?></button>
                        
                        <div>
                            <a class="uk-link" href="<?= baseUrl("users/register") ?>"><?= lang('auth.create_new_account') ?></a>
                            <span>-</span>
                            <a class="uk-link" href="<?= baseUrl("users/login") ?>"><?= lang('auth.login') ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</section>

<?= $this->stop(); ?>