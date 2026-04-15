<?= $this->Html->css('mastermind') ?>

<div class="page-shell">
    <div class="top-nav">
        <?= $this->Html->link(
            '← Accueil',
            ['controller' => 'Pages', 'action' => 'display', 'home'],
            ['class' => 'back-link']
        ) ?>
    </div>

    <div class="game-page">
        <div class="game-card">

            <h2>Mastermind</h2>
            <p class="subtitle">Devinez la combinaison secrète !</p>

            <div class="stats">
                <div class="stat-box">
                    <span class="label">Nombre de tentatives</span>
                    <span class="value">
                        <?= $userGame->score > 0 ? $userGame->score . ' essais' : 'Aucun' ?>
                    </span>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="<?= $game->status === 'finished' ? 'win' : 'result' ?>">
                    <?= h($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($game->status === 'finished'): ?>

                <div class="actions">
                    <?= $this->Html->link(
                        'Rejouer',
                        ['controller' => 'Games', 'action' => 'startMastermind'],
                        ['class' => 'btn']
                    ) ?>
                </div>

            <?php else: ?>

                <?= $this->Form->create() ?>

                <div class="form-group">
                    <?= $this->Form->control('guess', [
                        'label' => 'Votre tentative',
                        'maxlength' => 4,
                        'placeholder' => 'Ex : 1234',
                        'class' => 'input',
                        'labelOptions' => ['class' => 'form-label']
                    ]) ?>
                </div>

                <?= $this->Form->button('Valider', ['class' => 'btn']) ?>

                <?= $this->Form->end() ?>

            <?php endif; ?>

            <div class="help">
                <p>4 chiffres entre 0 et 9</p>
            </div>

        </div>
    </div>
</div>