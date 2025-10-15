<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ??  App\Engine\Libraries\Languages::primary() ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="baseurl" content="<?= baseUrl() ?>">
    <meta name="checkauth" content="<?= (!is_null(checkAuth()) ? 'false' : 'true') ?>">
    
    <link rel="stylesheet" href="<?= assetsUrl('css/uikit.min.css')?>">
    <link rel="stylesheet" href="<?= assetsUrl('css/main.min.css')?>">
   
    <script src="<?= assetsUrl('js/uikit.min.js') ?>"></script>
    <script src="<?= assetsUrl('js/uikit-icons.min.js') ?>"></script>

    <script src="<?= assetsUrl('js/tinymce/tinymce.min.js') ?>"></script>
    
    <title><?= $title ?? APPNAME; ?></title>
</head>
<body class="lang-<?= App\Engine\Libraries\Languages::active() ?>">

    <section>
        <div class="admin-content uk-padding">
            <?= $this->section('mainSection') ?>
        </div>
    </section>


    <?= $this->section('script') ?>

    <script type="module" src="<?= assetsUrl('js/adminBootstrap.js') ?>"></script>
</body>
</html>