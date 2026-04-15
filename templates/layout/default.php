<?php
$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>

    <?= $this->Html->meta('icon') ?>

    <!-- CSS FRAMEWORK -->
    <?= $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake']) ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS PERSO (IMPORTANT : à la fin pour écraser les autres) -->
    <?= $this->Html->css(['home', 'auth']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>

<body>
    <nav class="top-nav"></nav>

    <main class="main">
        <div class="container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <footer></footer>
</body>
</html>
