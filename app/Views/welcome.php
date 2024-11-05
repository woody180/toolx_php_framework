<?= $this->layout('partials/template', ['title' => $title]) ?>

<?= $this->start('mainSection') ?>
<section>
    <div>

        <h1><?= $title ?></h1>
        <p>Lorem, <a href="<?= url_to('HomeController@about', 'about', 1) ?>">About page</a>. Quo asperiores beatae officia accusamus quibusdam nobis ipsum repudiandae. Corporis odit voluptatem eveniet modi unde, nam fuga blanditiis harum, ut delectus optio.</p>    
        <p>Lorem, <a href="<?= url_to('HomeController@gallery') ?>">Gallery page</a>. Quo asperiores beatae officia accusamus quibusdam nobis ipsum repudiandae. Corporis odit voluptatem eveniet modi unde, nam fuga blanditiis harum, ut delectus optio.</p>    
            
    </div>
</section>

<?= $this->stop() ?>