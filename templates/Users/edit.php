<?= $this->Html->css('profile') ?>

<div class="profile-page">
    <div class="profile-card edit-card">

        <div class="profile-header">
            <h2>Modifier mon profil</h2>

            <?= $this->Html->link(
                '← Retour',
                ['action' => 'profile'],
                ['class' => 'btn btn-secondary']
            ) ?>
        </div>

        <?= $this->Form->create($user) ?>

        <div class="form-group">
            <?= $this->Form->control('username', [
                'label' => 'Nom d’utilisateur'
            ]) ?>
        </div>

        <div class="form-actions">
            <?= $this->Form->button('Enregistrer', ['class' => 'btn']) ?>
        </div>

        <?= $this->Form->end() ?>

    </div>
</div>