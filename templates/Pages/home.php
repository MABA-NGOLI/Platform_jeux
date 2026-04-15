<?= $this->Html->css('home') ?>


<div class="home-page">


    <header class="topbar">
        <div class="logo">
            🎮 <span>Plateforme de Jeux</span>
        </div>

        <div class="user-box">

             <?= $this->Html->link(
                'Profile',
                ['controller' => 'Users', 'action' => 'profile'],
                ['class' => 'logout-btn']
            ) ?>
             

            <?= $this->Html->link(
                'Déconnexion',
                ['controller' => 'Users', 'action' => 'logout'],
                ['class' => 'logout-btn']
            ) ?>
        </div>
    </header>

    <!-- TITLE -->
    <div class="welcome">
        <h1>Bienvenue, <?= h($user['username'] ?? 'User') ?> !</h1>!</h1>
        <p>Choisissez un jeu pour commencer</p>
    </div>

    <div class="games-grid">

        <!-- Mastermind -->
        <div class="game-card red">
            <div class="icon">🧠</div>
            <h3>Mastermind</h3>
            <p>Jeu solo</p>
            <p>Devinez la combinaison secrète</p>

            <?= $this->Html->link('Jouer', ['controller' => 'Games', 'action' => 'startMastermind'], ['class' => 'btn']) ?>
        </div>

        <!-- Filler -->
        <div class="game-card green">
            <div class="icon">▦</div>
            <h3>Filler</h3>
            <p>Multijoueur</p>
            <p>Conquérez le territoire</p>

            <?= $this->Html->link('Jouer', ['controller' => 'Games', 'action' => 'filler'], ['class' => 'btn']) ?>
        </div>

        <!-- Labyrinthe -->
        <div class="game-card blue">
            <div class="icon">🧭</div>
            <h3>Labyrinthe</h3>
            <p>Multijoueur</p>
            <p>Trouvez le trésor en premier</p>

            <?= $this->Html->link('Jouer', ['controller' => 'Games', 'action' => 'labyrinthe'], ['class' => 'btn']) ?>
        </div>

    </div>

</div>
