<?= $this->Html->css('home') ?>


<div class="home-page">


    <header class="topbar">
        <div class="logo">
            <i class="fas fa-gamepad"></i>
            <span>Plateforme de Jeux</span>
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

    <div class="welcome">
        <h1>Bienvenue, <?= h($user['username'] ?? 'User') ?> !</h1>
        <p>Choisissez un jeu pour commencer</p>
    </div>

    <div class="games-grid">

        <div class="game-card red">
            <div class="icon">
               <i class="fas fa-brain"></i>
            </div>
            <h3>Mastermind</h3>
            <p>Jeu solo</p>
            <p>Devinez la combinaison secrète</p>

            <?= $this->Html->link('Jouer', ['controller' => 'Mastermind', 'action' => 'startMastermind'], ['class' => 'btn']) ?>
        </div>

        <div class="game-card green">
        
            <div class="icon">
                <i class="fas fa-border-all"></i>
            </div>
            <h3>Filler</h3>
            <p>Multijoueur</p>
            <p>Conquérez le territoire</p>

            <?= $this->Html->link('Jouer', ['controller' => 'Filler', 'action' => 'startFiller'], ['class' => 'btn']) ?>
        </div>

        <div class="game-card blue">
            <div class="icon">
                <i class="fas fa-route"></i>
            </div>
            <h3>Labyrinthe</h3>
            <p>Multijoueur</p>
            <p>Trouvez le trésor en premier</p>

            <?= $this->Html->link('Jouer', ['controller' => 'Games', 'action' => 'startLabyrinth'], ['class' => 'btn']) ?>
        </div>

    </div>

</div>
