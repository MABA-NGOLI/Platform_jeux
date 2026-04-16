<?php
declare(strict_types=1);

namespace App\Controller;

class UsersController extends AppController
{
    // Liste des utilisateurs
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

    // Inscription
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

    // Connexion
    public function login()
    {
        // Supprime une éventuelle redirection cassée
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

    // Profil utilisateur
    public function profile()
    {
        $userSession = $this->request->getSession()->read('User');

        if (!$userSession) {
            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->get($userSession->id);
        $usersInGames = $this->fetchTable('UsersInGames');

        // Mastermind : parties terminées seulement
        $mastermindGames = $usersInGames->find()
            ->contain(['Games'])
            ->where([
                'UsersInGames.user_id' => $user->id,
                'Games.name' => 'mastermind',
                'Games.status' => 'finished'
            ])
            ->all()
            ->toList();

        $mastermindCount = count($mastermindGames);
        $mastermindBest = !empty($mastermindGames)
            ? min(array_map(fn($g) => (int)$g->score, $mastermindGames))
            : '-';

        // Filler : parties terminées seulement
        $fillerGames = $usersInGames->find()
            ->contain(['Games'])
            ->where([
                'UsersInGames.user_id' => $user->id,
                'Games.name' => 'filler',
                'Games.status' => 'finished'
            ])
            ->all()
            ->toList();

        $fillerCount = count($fillerGames);
        $fillerBest = !empty($fillerGames)
            ? max(array_map(fn($g) => (int)$g->score, $fillerGames))
            : '-';

        // Labyrinthe : toutes les parties pour compter les victoires
        $labyrinthGames = $usersInGames->find()
            ->contain(['Games'])
            ->where([
                'UsersInGames.user_id' => $user->id,
                'Games.name' => 'labyrinth'
            ])
            ->all()
            ->toList();

        $labyrinthCount = count($labyrinthGames);
        $labyrinthWins = count(array_filter($labyrinthGames, function ($g) {
            return (int)$g->is_winner === 1;
        }));

        $this->set(compact(
            'user',
            'mastermindCount',
            'mastermindBest',
            'fillerCount',
            'fillerBest',
            'labyrinthCount',
            'labyrinthWins'
        ));
    }

    // Modifier uniquement le username
    public function edit()
    {
        $userSession = $this->request->getSession()->read('User');

        if (!$userSession) {
            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->get($userSession->id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = [
                'username' => $this->request->getData('username')
            ];

            $user = $this->Users->patchEntity($user, $data);

            if ($this->Users->save($user)) {
                // met à jour la session avec le nouveau username
                $this->request->getSession()->write('User', $user);

                $this->Flash->success('Nom d’utilisateur mis à jour.');
                return $this->redirect(['action' => 'profile']);
            }

            $this->Flash->error('Impossible de mettre à jour le profil.');
        }

        $this->set(compact('user'));
    }

    // Déconnexion
    public function logout()
    {
        $this->request->getSession()->destroy();
        $this->Flash->success('Déconnexion réussie.');
        return $this->redirect(['action' => 'login']);
    }

    // Supprimer compte
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

    // Dashboard utilisateur
    public function dashboard()
    {
        $user = $this->request->getSession()->read('User');

        if (!$user) {
            $this->Flash->error('Veuillez vous connecter.');
            return $this->redirect(['action' => 'login']);
        }

        $this->set(compact('user'));
    }
}