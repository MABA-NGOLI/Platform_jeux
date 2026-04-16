<div class="profil">
    <h1>👤 <?= h($user->username) ?></h1>
    <p style="color:#a0a0b0;margin-bottom:2rem">Membre depuis <?= $user->created->format('d/m/Y') ?></p>

    <!-- Résumé meilleurs scores -->
    <div class="game-grid" style="margin-bottom:2rem">
        <div class="game-card">
            <h2>🧠 Mastermind</h2>
            <p>Meilleur score : <strong>
                <?= $bestMastermind ? $bestMastermind->score . ' coups' : 'Aucun' ?>
            </strong></p>
        </div>
        <div class="game-card">
            <h2>🎨 Filler</h2>
            <p>Meilleur score : <strong>
                <?= $bestFiller ? $bestFiller->score . ' cases' : 'Aucun' ?>
            </strong></p>
        </div>
        <div class="game-card">
            <h2>🗺️ Labyrinthe</h2>
            <p>Victoires : <strong><?= $winsLabyrinthe ?></strong></p>
        </div>
    </div>

    <!-- Boutons si propriétaire -->
    <?php if ($isOwner): ?>
        <div style="margin-bottom:1.5rem;display:flex;gap:1rem">
            <?= $this->Html->link('✏️ Modifier le profil', ['action' => 'edit'], ['class' => 'btn btn-secondary']) ?>
            <?= $this->Form->postLink('🗑️ Supprimer le compte', ['action' => 'delete'],
                ['class' => 'btn btn-primary',
                 'confirm' => 'Êtes-vous sûr de vouloir supprimer votre compte ?']) ?>
        </div>
    <?php endif; ?>

    <!-- Historique complet -->
    <h2 style="margin-bottom:1rem">Historique des parties</h2>
    <table class="score-table">
        <thead>
            <tr>
                <th>Jeu</th>
                <th>Score</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scores as $score): ?>
            <tr>
                <td><?= h(ucfirst($score->game)) ?></td>
                <td><?= h($score->score) ?></td>
                <td><?= $score->created->format('d/m/Y H:i') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($scores->toArray())): ?>
            <tr><td colspan="3" style="text-align:center;color:#666">Aucune partie jouée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>