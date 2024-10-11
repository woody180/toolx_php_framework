<?php $this->layout('partials/template', ['title' => $title]) ?>

<?= $this->start('mainSection') ?>
    <section class="uk-section">
        <div class="uk-container uk-container-small">
            
            <div class="uk-card uk-card-default">
                <div class="uk-card-body">
                    
                    <div class="uk-flex uk-flex-between uk-flex-middle">
                        <p class="uk-text-lead"><?= lang('auth.profile_edit') ?></p>
                        <a href="<?= baseUrl("users/profile/" . $_SESSION['userid']) ?>"><span uk-icon="icon: arrow-left; ratio: 1"></span> <?= lang('auth.back_to_profile') ?></a>
                    </div>
                    
                    <?php if (hasFlashData('error')): ?>
                    <div class="uk-alert-danger" uk-alert>
                        <a class="uk-alert-close" uk-close></a>
                        <p><?= getFlashData('error'); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (hasFlashData('message')): ?>
                    <div class="uk-alert-info" uk-alert>
                        <a class="uk-alert-close" uk-close></a>
                        <p><?= getFlashData('message'); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (hasFlashData('success')): ?>
                    <div class="uk-alert-success" uk-alert>
                        <a class="uk-alert-close" uk-close></a>
                        <p><?= getFlashData('success'); ?></p>
                    </div>
                    <?php endif; ?>

                    <form enctype="multipart/form-data" id="alter-login-form" class="alter-login-form uk-grid-medium uk-child-width-1-1k" uk-grid action="<?= baseUrl("users/account/" . $_SESSION['userid']) ?>" method="POST" accept-charset="utf-8">
                        
                        <?= csrf_field() ?>
                        
                        <div>
                            <label for="name" class="uk-form-label"><?= lang('auth.name') ?></label>
                            <input id="name" type="text" name="name" class="uk-input" value="<?= $user->name ?>">
                            <p class="uk-margin-remove uk-text-danger uk-text-small"><?= implode(', ', getFlashData('errors')->name ?? []) ?></p>
                        </div>
                        
                        <div>
                            <label for="username" class="uk-form-label"><?= lang('auth.username') ?></label>
                            <input id="username" type="text" name="username" class="uk-input" value="<?= $user->username ?>">
                            <p class="uk-margin-remove uk-text-danger uk-text-small"><?= implode(', ', getFlashData('errors')->username ?? []) ?></p>
                        </div>
                        
                        <div>
                            <label for="email" class="uk-form-label"><?= lang('auth.email') ?></label>
                            <input id="email" type="email" name="email" class="uk-input" value="<?= $user->email ?>">
                            <p class="uk-margin-remove uk-text-danger uk-text-small"><?= implode(', ', getFlashData('errors')->email ?? []) ?></p>
                        </div>
                        
                        <div>
                            <input type="hidden" name="avatar_hidden" value="<?= $user->avatar ?>">
                            <label for="" class="uk-form-label"><?= lang('auth.choose_avatar') ?></label>
                            <div class="uk-placeholder uk-margin-remove uk-text-center">
                                <span uk-icon="icon: cloud-upload"></span>
                                <span class="uk-text-middle"><?= lang('auth.set_profile_image') ?> -</span>
                                <div uk-form-custom>
                                    <input name="avatar" type="file">
                                    <span class="uk-link"><?= lang('auth.choose_avatar') ?></span>
                                </div>
                            </div>
                            <p class="uk-margin-remove uk-text-danger uk-text-small"><?= implode(', ', getFlashData('errors')->avatar ?? []) ?></p>
                        </div>
                    
                        <div>
                            <label for="password" class="uk-form-label"><?= lang('auth.password') ?></label>
                            <input id="password" type="password" name="password" class="uk-input" value="">
                            <p class="uk-margin-remove uk-text-danger uk-text-small"><?= implode(', ', getFlashData('errors')->password ?? []) ?></p>
                        </div>
                        
                        <div>
                            <label for="password" class="uk-form-label"><?= lang('auth.repeat_password') ?></label>
                            <input id="password" type="password" name="password_repeat" class="uk-input" value="">
                            <p class="uk-margin-remove uk-text-danger uk-text-small"><?= implode(', ', getFlashData('errors')->password_repeat ?? []) ?></p>
                        </div>
                        
                        <div id="account-buttons-set" class="uk-flex uk-flex-between uk-flex-middle">
                            <button class="uk-button uk-button-primary" type="submit"><?= lang('auth.update_profile') ?></button>
                            
                            <a href="<?= baseUrl("users/profile/" . $_SESSION['userid']) ?>"><span uk-icon="icon: arrow-left; ratio: 1"></span> <?= lang('auth.back_to_profile') ?></a>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </section>

<?= $this->stop(); ?>