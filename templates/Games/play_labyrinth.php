<?= $this->Html->css('labyrinth') ?>

<div class="top-nav">
    <?= $this->Html->link(
        '← Retour',
        ['controller' => 'Pages', 'action' => 'display', 'home'],
        ['class' => 'back-link']
    ) ?>
</div>

<div class="lab-page">
    <div class="lab-layout">

        <div class="lab-panel">
            <h2>Labyrinthe</h2>
            <p class="subtitle">Trouvez le trésor avant l'autre joueur</p>

            <div class="info-card">
                <strong>PA :</strong> <?= $currentPlayer->action_points ?>
            </div>

            <?php if ($joinLink): ?>
                <div class="info-card">
                    <p>En attente d'un 2e joueur</p>
                    <input type="text" readonly value="<?= h($joinLink) ?>">
                </div>
            <?php endif; ?>

            <?php if ($game->status === 'playing'): ?>
                <div class="moves">
                    <?= $this->Html->link('↑', ['controller' => 'Games', 'action' => 'moveLabyrinth', $game->id, 'up'], ['class' => 'move-btn']) ?>
                    <div class="horizontal">
                        <?= $this->Html->link('←', ['controller' => 'Games', 'action' => 'moveLabyrinth', $game->id, 'left'], ['class' => 'move-btn']) ?>
                        <?= $this->Html->link('→', ['controller' => 'Games', 'action' => 'moveLabyrinth', $game->id, 'right'], ['class' => 'move-btn']) ?>
                    </div>
                    <?= $this->Html->link('↓', ['controller' => 'Games', 'action' => 'moveLabyrinth', $game->id, 'down'], ['class' => 'move-btn']) ?>
                </div>
            <?php endif; ?>

            <?php if ($game->status === 'finished'): ?>
                <div class="win-box">
                    Partie terminée !
                </div>
            <?php endif; ?>
        </div>

        <div class="map-wrapper">
            <div class="map-grid">
                <?php foreach ($map as $y => $row): ?>
                    <div class="map-row">
                        <?php foreach ($row as $x => $cell): ?>
                            <?php
                                $class = ($cell === '#') ? 'wall' : 'path';
                                $content = '';

                                if ($x == $settings->treasure_x && $y == $settings->treasure_y) {
                                    $content = '💎';
                                }

                                foreach ($players as $p) {
                                    if ((int)$p->pos_x === (int)$x && (int)$p->pos_y === (int)$y) {
                                        $content = ((int)$p->user_id === (int)$currentPlayer->user_id) ? '🙂' : '😈';
                                    }
                                }
                            ?>
                            <div class="cell <?= $class ?>">
                                <?= $content ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($game->status === 'playing'): ?>
            <script>
                setTimeout(() => location.reload(), 3000);
            </script>
        <?php endif; ?>

    </div>
</div>