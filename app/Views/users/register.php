<?php $this->layout('partials/template', ['title' => $title]) ?>

<?= $this->start('mainSection') ?>

<section class="uk-section">
    <div class="uk-container uk-container-small">
        
        <div class="uk-card uk-card-default">
            <div class="uk-card-body">
                <h1><?= lang('auth.registration') ?></h1>
                
                <?php if (hasFlashData('error')): ?>
                <div class="uk-alert-danger" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p><?= getFlashData('error'); ?></p>
                </div>
                <?php endif; ?>

                <form enctype="multipart/form-data" id="alter-login-form" class="alter-login-form uk-grid-medium uk-child-width-1-1k" uk-grid action="<?= baseUrl("users/register") ?>" method="POST" accept-charset="utf-8">
                    <?= csrf_field() ?>
                    <div>
                        <label for="name" class="uk-form-label"><?= lang('auth.name') ?></label>
                        <input id="name" type="text" name="name" class="uk-input" value="<?= getForm('name') ?>">
                        <?= show_error('errors', 'name') ?>
                    </div>
                    
                    <div>
                        <label for="username" class="uk-form-label"><?= lang('auth.username') ?></label>
                        <input id="username" type="text" name="username" class="uk-input" value="<?= getForm('username') ?>">
                        <?= show_error('errors', 'username') ?>
                    </div>
                    
                    <div>
                        <label for="email" class="uk-form-label"><?= lang('auth.email') ?></label>
                        <input id="email" type="email" name="email" class="uk-input" value="<?= getForm('email') ?>">
                        <?= show_error('errors', 'email') ?>
                    </div>
                    
                    <div>
                        <label for="" class="uk-form-label"><?= lang('auth.avatar') ?></label>
                        <div class="uk-placeholder uk-margin-remove uk-text-center">
                            <span uk-icon="icon: cloud-upload"></span>
                            <!--<span class="uk-text-middle"><?= lang('auth.profile_picture') ?> -</span>-->
                            <div uk-form-custom>
                                <input name="avatar" type="file">
                                <span class="uk-link"><?= lang('auth.choose_avatar') ?></span>
                            </div>
                        </div>
                        <?= show_error('errors', 'avatar') ?>
                    </div>
                   
                    <div>
                        <label for="password" class="uk-form-label"><?= lang('auth.password') ?></label>
                        <input id="password" type="password" name="password" class="uk-input" value="<?= getForm('password') ?>">
                        <?= show_error('errors', 'password') ?>
                    </div>
                    
                    <div>
                        <label for="password" class="uk-form-label"><?= lang('auth.repeat_password') ?></label>
                        <input id="password" type="password" name="password_repeat" class="uk-input" value="<?= getForm('password_repeat') ?>">
                        <?= show_error('errors', 'password_repeat') ?>
                    </div>
                    
                    <div id="account-buttons-set" class="uk-flex uk-flex-between uk-flex-middle">
                        <button class="uk-button uk-button-primary" type="submit"><?= lang('auth.register') ?></button>
                        
                        <div>
                            <a class="uk-link" href="<?= baseUrl("users/login") ?>"><?= lang('auth.already_registered') ?></a>
                            <span>-</span>
                            <a class="uk-link" href="<?= baseUrl("users/reset") ?>"><?= lang('auth.forgot_password') ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</section>

<?= $this->stop() ?>