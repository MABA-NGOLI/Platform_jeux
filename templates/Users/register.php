<?= $this->Html->css('auth') ?>

<div class="auth-page">

    <div class="login-container">

        <h2>Créer un compte</h2>

        <p class="subtitle">Rejoignez la plateforme de jeux</p>

        <?= $this->Flash->render() ?>

        <?= $this->Form->create($user, [
            'class' => 'auth-form'
        ]) ?>

        <div class="form-group">
            <?= $this->Form->control('username', [
                'label' => 'Nom utilisateur',
                'placeholder' => 'Ex: joueur123',
                'required' => true
            ]) ?>
        </div>

        <div class="form-group">
            <?= $this->Form->control('email', [
                'label' => 'Email',
                'placeholder' => 'exemple@mail.com',
                'required' => true
            ]) ?>
        </div>

        <div class="form-group">
            <?= $this->Form->control('password', [
                'label' => 'Mot de passe',
                'type' => 'password',
                'placeholder' => '........',
                'required' => true
            ]) ?>
        </div>

        <?= $this->Form->button('S’inscrire', [
            'class' => 'btn'
        ]) ?>

        <?= $this->Form->end() ?>

        <div class="register">
            <p>
                Déjà inscrit ?
                <?= $this->Html->link('Connexion', ['action' => 'login']) ?>
            </p>
        </div>

    </div>

</div>
