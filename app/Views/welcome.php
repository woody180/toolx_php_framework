<?= $this->layout('partials/template', ['title' => $title]) ?>

<?= $this->start('mainSection') ?>
<section>
    <div>

        <h1><?= $title ?></h1>
        <p>Go to page, <a href="<?= url_to('HomeController@about', 'about', 1) ?>">About page</a>.</p>    
        <p>Or take a look at <a href="<?= url_to('HomeController@gallery') ?>">Gallery page</a>.</p>
        <a href="<?= baseUrl('file-manager') ?>">Open editor page</a> OR <a href="#" onclick="window.renderFileManager(); return false;">Open file manager</a>
            
    </div>
</section>

<?= $this->stop() ?>