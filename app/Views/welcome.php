<?= $this->layout('partials/template', ['title' => $title]) ?>

<?= $this->start('mainSection') ?>
<section>
    <div>

        <h1><?= $title ?></h1>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quo asperiores beatae officia accusamus quibusdam nobis ipsum repudiandae. Corporis odit voluptatem eveniet modi unde, nam fuga blanditiis harum, ut delectus optio.</p>    
        
        <ul>
            <?php foreach ($articles as $article): ?>
                <li><?= $article->title ?></li>
            <?php endforeach; ?>
        </ul>
            
    </div>
</section>

<?= $this->stop() ?>