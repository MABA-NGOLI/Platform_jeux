<?= $this->Html->css('filler') ?>

<div class="top-nav">
    <?= $this->Html->link(
        '← Accueil',
        ['controller' => 'Pages', 'action' => 'display', 'home'],
        ['class' => 'back-link']
    ) ?>
</div>

<div class="filler-page">
    <div class="filler-layout">

        <div class="filler-panel">
            <h2>Filler</h2>

            <p class="subtitle">Étendez votre territoire en choisissant une couleur.</p>

            <div class="score-boxes">
                <div class="score-box player-one">
                    <span class="label">Joueur 1</span>
                    <span class="value"><?= $playerOneScore ?> cases</span>
                </div>

                <div class="score-box player-two">
                    <span class="label">Joueur 2</span>
                    <span class="value"><?= $playerTwoScore ?> cases</span>
                </div>
            </div>

            <?php if ($game->status === 'waiting'): ?>
                <div class="info-box">
                    En attente d’un deuxième joueur.
                </div>

                <?php if ($joinLink): ?>
                    <div class="join-box">
                        <p>Lien pour rejoindre :</p>
                        <input type="text" readonly value="<?= h($joinLink) ?>">
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($game->status === 'playing'): ?>
                <div class="info-box">
                    <?php if ((int)$settings->current_turn_user_id === ($isPlayerOne ? $settings->player_one_id : $settings->player_two_id)): ?>
                        À votre tour.
                    <?php else: ?>
                        Tour de l’adversaire.
                    <?php endif; ?>
                </div>

                <div class="color-picker">
                    <?php foreach ($availableColors as $color): ?>
                        <?= $this->Html->link(
                            '',
                            ['controller' => 'Games', 'action' => 'chooseFillerColor', $game->id, $color],
                            ['class' => 'color-btn ' . $color]
                        ) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($game->status === 'finished'): ?>
                <div class="win-box">
                    <?php if ($settings->winner_user_id): ?>
                        Partie terminée.
                    <?php else: ?>
                        Égalité.
                    <?php endif; ?>
                </div>

                <?= $this->Html->link(
                    'Créer une nouvelle partie',
                    ['controller' => 'Games', 'action' => 'startFiller'],
                    ['class' => 'main-btn']
                ) ?>
            <?php endif; ?>
        </div>

        <div class="board-wrapper">
            <div class="board">
                <?php foreach ($board as $row): ?>
                    <div class="board-row">
                        <?php foreach ($row as $cell): ?>
                            <div class="cell <?= h($cell['color']) ?> <?= !empty($cell['owner']) ? 'owned owner-' . $cell['owner'] : '' ?>"></div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Auto-refresh pendant le jeu -->
        <?php if ($game->status === 'playing'): ?>
        <script>
            setTimeout(function () {
                window.location.reload();
            }, 2000);
        </script>
        <?php endif; ?>

    </div>
</div>