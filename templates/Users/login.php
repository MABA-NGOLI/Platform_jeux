<?= $this->Html->css('auth') ?>

<div class="auth-page">

    <div class="login-container">

        <div class="icon">
            🎮
        </div>

        <h2>Plateforme de Jeux</h2>
        <p>Connectez-vous pour jouer</p>

        <?= $this->Flash->render() ?>

        <?= $this->Form->create(null) ?>

        <div class="form-group">
            <?= $this->Form->label('email', 'Email') ?>
            <?= $this->Form->control('email', [
                'label' => false,
                'placeholder' => 'exemple@mail.com',
                'required' => true
            ]) ?>
        </div>

        <div class="form-group">
            <?= $this->Form->label('password', 'Mot de passe') ?>
            <?= $this->Form->control('password', [
                'label' => false,
                'type' => 'password',
                'placeholder' => '........',
                'required' => true
            ]) ?>
        </div>

        <?= $this->Form->button('→ Se connecter', [
            'class' => 'btn'
        ]) ?>

        <?= $this->Form->end() ?>

        <div class="register">
            <p>
                Pas de compte ?
                <?= $this->Html->link('Créez-en un compte', ['action' => 'register']) ?>
            </p>
        </div>

    </div>

</div>
