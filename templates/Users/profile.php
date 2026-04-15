<div class="auth-page">

  <div class="auth-card">

    <h1>Mon Profil</h1>
    <p class="subtitle">Espace joueur</p>

    <!-- 👤 Infos utilisateur -->
    <div class="profile-info">

      <p><strong>Nom :</strong> <?= h($user['username']) ?></p>

      <p><strong>Email :</strong> <?= h($user['email']) ?></p>

    </div>

    <!-- 🎮 Actions -->
    <div class="profile-actions">

      <a href="/users/logout" class="btn-primary">
        Déconnexion
      </a>

      <a href="/users/dashboard" class="btn-secondary">
        Accéder aux jeux
      </a>

    </div>

  </div>

</div>
