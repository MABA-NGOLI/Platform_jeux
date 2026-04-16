<?php
declare(strict_types=1);

namespace App\Controller;

class MastermindController extends AppController
{
    protected $Games;

    public function initialize(): void
    {
        parent::initialize();

        $this->Games = $this->fetchTable('Games');
    }

    public function startMastermind()
    {
        $user = $this->request->getSession()->read('User');

        if (!$user) {
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $game = $this->Games->newEmptyEntity();
        $game->name = 'mastermind';
        $game->status = 'playing';
        $game->secret_code = (string)rand(1111, 6666);

        if (!$this->Games->save($game)) {
            debug($game->getErrors());
            die;
        }

        $userGame = $this->Games->UsersInGames->newEmptyEntity();
        $userGame->user_id = $user->id;
        $userGame->game_id = $game->id;
        $userGame->score = 0;
        $userGame->attempts = 0;

        if (!$this->Games->UsersInGames->save($userGame)) {
            debug($userGame->getErrors());
            die;
        }

        return $this->redirect([
            'controller' => 'Mastermind',
            'action' => 'playMastermind',
            $game->id
        ]);

         $boardGame = $this->Games->BoardGames->find()
        ->where(['slug' => 'mastermind'])
        ->first();

        $game = $this->Games->newEmptyEntity();
        $game->name = 'mastermind';
        $game->board_game_id = $boardGame ? $boardGame->id : null;
        $game->status = 'playing';
        $game->secret_code = (string)rand(1111, 6666);

    }

    public function playMastermind($id)
    {
        $user = $this->request->getSession()->read('User');

        if (!$user) {
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $game = $this->Games->get($id);

        $userGame = $this->Games->UsersInGames
            ->find()
            ->where([
                'user_id' => $user->id,
                'game_id' => $id
            ])
            ->first();

        if (!$userGame) {
            $this->Flash->error('Partie introuvable pour ce joueur');
            return $this->redirect(['action' => 'startMastermind']);
        }

        $message = null;

        if ($this->request->is('post')) {
            $guess = (string)$this->request->getData('guess');

            $userGame->attempts++;
            $userGame->score++;

            $message = $this->check($guess, $game->secret_code);

            if ($guess === $game->secret_code) {
                $game->status = 'finished';
                $this->Games->save($game);

                $message = "Bravo terminé en {$userGame->attempts} essais";
            }

            $this->Games->UsersInGames->save($userGame);
        }

        $this->set(compact('game', 'userGame', 'message'));
    }

    private function check($guess, $secret)
    {
        $correct = 0;
        $misplaced = 0;

        $g = str_split($guess);
        $s = str_split($secret);

        foreach ($g as $i => $v) {
            if (isset($s[$i]) && $v === $s[$i]) {
                $correct++;
                unset($s[$i], $g[$i]);
            }
        }

        foreach ($g as $v) {
            if (in_array($v, $s, true)) {
                $misplaced++;
                unset($s[array_search($v, $s, true)]);
            }
        }

        return "$correct bien placés / $misplaced mal placés";
    }
}