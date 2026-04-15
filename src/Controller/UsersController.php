<?php
declare(strict_types=1);

namespace App\Controller;

class UsersController extends AppController
{
    // Liste des utilisateurs (option admin/debug)
    public function index()
    {
        $users = $this->paginate($this->Users->find());
        $this->set(compact('users'));
    }

    // Voir un utilisateur
    public function view($id = null)
    {
        $user = $this->Users->get($id);
        $this->set(compact('user'));
    }

    // 📝 Inscription
    public function register()
    {
        $user = $this->Users->newEmptyEntity();

        if ($this->request->is('post')) {

            $user = $this->Users->patchEntity($user, $this->request->getData());

            if ($this->Users->save($user)) {

                $this->Flash->success('Compte créé avec succès.');

                return $this->redirect(['action' => 'login']);
            }

            $this->Flash->error('Erreur lors de l’inscription.');
        }

        $this->set(compact('user'));
    }

    //  Connexion
    public function login()
{
    // 🔥 supprime redirection automatique cassée
    $this->request->getSession()->delete('Auth.redirect');

    if ($this->request->is('post')) {

        $email = $this->request->getData('email');
        $password = $this->request->getData('password');

        $user = $this->Users->find()
            ->where(['email' => $email])
            ->first();

        if ($user && password_verify($password, $user->password)) {

            $this->request->getSession()->write('User', $user);

            $this->Flash->success('Connexion réussie.');

            return $this->redirect('/');
        }

        $this->Flash->error('Email ou mot de passe incorrect.');
    }
}

    // 👤 Profil utilisateur
    public function profile()
    {
        $user = $this->request->getSession()->read('User');

        if (!$user) {
            $this->Flash->error('Veuillez vous connecter.');
            return $this->redirect(['action' => 'login']);
        }

        $this->set(compact('user'));
    }

    //  Modifier profil
    public function edit()
    {
        $userSession = $this->request->getSession()->read('User');

        if (!$userSession) {
            $this->Flash->error('Accès refusé.');
            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->get($userSession->id);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $user = $this->Users->patchEntity($user, $this->request->getData());

            if ($this->Users->save($user)) {

                // mise à jour session
                $this->request->getSession()->write('User', $user);

                $this->Flash->success('Profil mis à jour.');

                return $this->redirect(['action' => 'profile']);
            }

            $this->Flash->error('Erreur lors de la modification.');
        }

        $this->set(compact('user'));
    }

    //  Déconnexion
    public function logout()
    {
        $this->request->getSession()->destroy();

        $this->Flash->success('Déconnexion réussie.');

        return $this->redirect(['action' => 'login']);
    }

    //  Supprimer compte
    public function delete()
    {
        $userSession = $this->request->getSession()->read('User');

        if (!$userSession) {
            $this->Flash->error('Vous devez être connecté.');
            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->get($userSession->id);

        if ($this->Users->delete($user)) {

            $this->request->getSession()->destroy();

            $this->Flash->success('Compte supprimé avec succès.');

            return $this->redirect(['action' => 'register']);
        }

        $this->Flash->error('Impossible de supprimer le compte.');

        return $this->redirect(['action' => 'profile']);
    }


    // Dashboard utilisateur (exemple d’accès aux jeux)
    public function dashboard()
{
    $user = $this->request->getSession()->read('User');

    // Si pas connecté, rediriger vers login
    if (!$user) {
        $this->Flash->error('Veuillez vous connecter.');
        return $this->redirect(['action' => 'login']);
    }

    $this->set(compact('user'));
}
}
