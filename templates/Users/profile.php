<?= $this->Html->css('profile') ?>

<div class="profile-page">

    <div class="profile-card">
        <div class="profile-header">
            <div>
                <h2><?= h($user->username) ?></h2>
                <p><?= h($user->email) ?></p>
            </div>

            <?= $this->Html->link(
                'Modifier',
                ['controller' => 'Users', 'action' => 'edit'],
                ['class' => 'btn']
            ) ?>
        </div>
    </div>

    <div class="games-grid">

    <div class="game-card red">
        <div class="icon">
            <i class="fas fa-brain"></i>
        </div>
        <h3>Mastermind</h3>
        <p>Parties jouées : <strong><?= $mastermindCount ?></strong></p>
        <p>Meilleur score : <strong><?= $mastermindBest ?></strong></p>
    </div>

    <div class="game-card green">
        <div class="icon">
            <i class="fas fa-border-all"></i>
        </div>
        <h3>Filler</h3>
        <p>Parties jouées : <strong><?= $fillerCount ?></strong></p>
        <p>Meilleur score : <strong><?= $fillerBest ?></strong></p>
    </div>

    <div class="game-card blue">
        <div class="icon">
            <i class="fas fa-route"></i>
        </div>
        <h3>Labyrinthe</h3>
        <p>Parties jouées : <strong><?= $labyrinthCount ?></strong></p>
        <p>Victoires : <strong><?= $labyrinthWins ?></strong></p>
    </div>

</div>

</div>