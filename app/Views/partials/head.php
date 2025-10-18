<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="baseurl" content="<?= baseUrl() ?>">
    <meta name="checkauth" content="<?= isset($_SESSION['userid']) ? 'true' : 'false' ?>">

    <?= loadUIKit() ?>

    <title><?= $title ?? APPNAME; ?></title>
</head>
<body>

    <?php $this->insert('partials/header') ?>