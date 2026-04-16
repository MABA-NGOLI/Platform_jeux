<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Routing\Router;

class GamesController extends AppController
{
    

//la partie de labyrinthes


public function startLabyrinth()
{
    $user = $this->request->getSession()->read('User');

    if (!$user) {
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    $mapPath = WWW_ROOT . 'files' . DS . 'maps' . DS . 'labyrinth1.txt';

    if (!file_exists($mapPath)) {
        die('Fichier de labyrinthe introuvable');
    }

    $lines = file($mapPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $map = [];

    foreach ($lines as $line) {
        $map[] = str_split($line);
    }

    // positions départ côte à côte
    $playerOneX = 1;
    $playerOneY = 1;
    $playerTwoX = 2;
    $playerTwoY = 1;

    // trésor
    $treasureX = 8;
    $treasureY = 5;

    $game = $this->Games->newEmptyEntity();
    $game->name = 'labyrinth';
    $game->status = 'waiting';
    $game->secret_code = null;

    if (!$this->Games->save($game)) {
        debug($game->getErrors());
        die;
    }

    $settings = $this->Games->LabyrinthSettings->newEmptyEntity();
    $settings->game_id = $game->id;
    $settings->map_data = json_encode($map);
    $settings->treasure_x = $treasureX;
    $settings->treasure_y = $treasureY;

    if (!$this->Games->LabyrinthSettings->save($settings)) {
        debug($settings->getErrors());
        die;
    }

    $userGame = $this->Games->UsersInGames->newEmptyEntity();
    $userGame->user_id = $user->id;
    $userGame->game_id = $game->id;
    $userGame->score = 0;
    $userGame->attempts = 0;
    $userGame->pos_x = $playerOneX;
    $userGame->pos_y = $playerOneY;
    $userGame->action_points = 10;
    $userGame->last_pa_gain = date('Y-m-d H:i:s');
    $userGame->is_winner = 0;

    if (!$this->Games->UsersInGames->save($userGame)) {
        debug($userGame->getErrors());
        die;
    }

    return $this->redirect([
        'controller' => 'Games',
        'action' => 'playLabyrinth',
        $game->id
    ]);

    $boardGame = $this->Games->BoardGames->find()
    ->where(['slug' => 'labyrinth'])
    ->first();

    $game = $this->Games->newEmptyEntity();
    $game->name = 'labyrinth';
    $game->board_game_id = $boardGame ? $boardGame->id : null;
    $game->status = 'waiting';
    $game->secret_code = null;




}

public function joinLabyrinth($id)
{
    $user = $this->request->getSession()->read('User');

    if (!$user) {
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    $game = $this->Games->get($id);

    if ($game->name !== 'labyrinth') {
        return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }

    $existing = $this->Games->UsersInGames->find()
        ->where(['game_id' => $id])
        ->count();

    if ($existing >= 2) {
        $this->Flash->error('La partie est déjà complète.');
        return $this->redirect(['action' => 'playLabyrinth', $id]);
    }

    $alreadyIn = $this->Games->UsersInGames->find()
        ->where([
            'game_id' => $id,
            'user_id' => $user->id
        ])
        ->first();

    if ($alreadyIn) {
        return $this->redirect(['action' => 'playLabyrinth', $id]);
    }

    $userGame = $this->Games->UsersInGames->newEmptyEntity();
    $userGame->user_id = $user->id;
    $userGame->game_id = $id;
    $userGame->score = 0;
    $userGame->attempts = 0;
    $userGame->pos_x = 2;
    $userGame->pos_y = 1;
    $userGame->action_points = 10;
    $userGame->last_pa_gain = date('Y-m-d H:i:s');
    $userGame->is_winner = 0;

    if (!$this->Games->UsersInGames->save($userGame)) {
        debug($userGame->getErrors());
        die;
    }

    $game->status = 'playing';

    if (!$this->Games->save($game)) {
        debug($game->getErrors());
        die;
    }

    return $this->redirect(['action' => 'playLabyrinth', $id]);
}

//regenération des points d'action toutes les 20 secondes, 5 points à chaque fois, max 15 points
    private function regenerateActionPoints($userGame)
    {
        $maxPoints = 18;        // maximum de PA
        $gainAmount = 5;        // nombre de PA gagnés
        $intervalSeconds = 10;  // toutes les 10 secondes

        // Si jamais la date n'existe pas encore
        if (empty($userGame->last_pa_gain)) {
            $userGame->last_pa_gain = date('Y-m-d H:i:s');
            return $userGame;
        }

        $lastGain = strtotime((string)$userGame->last_pa_gain);
        $now = time();

        if ($lastGain === false) {
            $userGame->last_pa_gain = date('Y-m-d H:i:s');
            return $userGame;
        }

        // Temps écoulé depuis le dernier gain
        $elapsed = $now - $lastGain;

        // Nombre de cycles de 10 secondes passés
        $cycles = intdiv($elapsed, $intervalSeconds);

        if ($cycles <= 0) {
            return $userGame;
        }

        // Nombre de PA à ajouter
        $pointsToAdd = $cycles * $gainAmount;

        // Ajout avec limite à 15
        $userGame->action_points = min(
            $maxPoints,
            (int)$userGame->action_points + $pointsToAdd
        );

        // On avance le timestamp seulement du nombre exact de cycles utilisés
        $userGame->last_pa_gain = date(
            'Y-m-d H:i:s',
            $lastGain + ($cycles * $intervalSeconds)
        );

        return $userGame;
    }


    public function playLabyrinth($id)
    {
        $user = $this->request->getSession()->read('User');

        if (!$user) {
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $game = $this->Games->get($id, contain: ['LabyrinthSettings']);

        if (!$game->labyrinth_settings) {
            $this->Flash->error('Paramètres du labyrinthe introuvables.');
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }

        $settings = $game->labyrinth_settings;
        $map = json_decode((string)$settings->map_data, true);

        // Récupère tous les joueurs de la partie
        $players = $this->Games->UsersInGames->find()
            ->where(['game_id' => $id])
            ->all()
            ->toList();

        // Régénère les PA de tous les joueurs
        foreach ($players as $i => $p) {
            $players[$i] = $this->regenerateActionPoints($p);
            $this->Games->UsersInGames->save($players[$i]);
        }

        // Trouver le joueur courant
        $currentPlayer = null;
        foreach ($players as $p) {
            if ((int)$p->user_id === (int)$user->id) {
                $currentPlayer = $p;
                break;
            }
        }

        if (!$currentPlayer) {
            $this->Flash->error('Vous ne faites pas partie de cette partie.');
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }

        $joinLink = null;
        if (count($players) < 2) {
            $joinLink = Router::url([
                'controller' => 'Games',
                'action' => 'joinLabyrinth',
                $game->id
            ], true);
        }

        $this->set(compact('game', 'settings', 'map', 'players', 'currentPlayer', 'joinLink'));
    }

    public function moveLabyrinth($id, $direction)
{
    $user = $this->request->getSession()->read('User');

    if (!$user) {
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    $game = $this->Games->get($id, contain: ['LabyrinthSettings']);
    $settings = $game->labyrinth_settings;
    $map = json_decode((string)$settings->map_data, true);

    $player = $this->Games->UsersInGames->find()
        ->where([
            'game_id' => $id,
            'user_id' => $user->id
        ])
        ->first();

    if (!$player) {
        return $this->redirect(['action' => 'playLabyrinth', $id]);
    }

    if ($game->status !== 'playing') {
        return $this->redirect(['action' => 'playLabyrinth', $id]);
    }

    // Régénère les PA avant le déplacement
    $player = $this->regenerateActionPoints($player);
    $this->Games->UsersInGames->save($player);

    // Vérifie qu'il a assez de PA
    if ((int)$player->action_points <= 0) {
        $this->Flash->error('Vous n’avez plus de points d’action.');
        return $this->redirect(['action' => 'playLabyrinth', $id]);
    }

    $x = (int)$player->pos_x;
    $y = (int)$player->pos_y;

    $newX = $x;
    $newY = $y;

    switch ($direction) {
        case 'up':
            $newY--;
            break;
        case 'down':
            $newY++;
            break;
        case 'left':
            $newX--;
            break;
        case 'right':
            $newX++;
            break;
    }

    // Vérifie si la nouvelle case existe et n'est pas un mur
    if (!isset($map[$newY][$newX]) || $map[$newY][$newX] === '#') {
        $this->Flash->error('Déplacement impossible.');
        return $this->redirect(['action' => 'playLabyrinth', $id]);
    }

    // Déplacement
    $player->pos_x = $newX;
    $player->pos_y = $newY;

    // Coût du mouvement = 1 PA
    $player->action_points = max(0, (int)$player->action_points - 1);

    // Vérifie si le joueur a trouvé le trésor
    if ($newX === (int)$settings->treasure_x && $newY === (int)$settings->treasure_y) {
        $player->is_winner = 1;
        $player->score = 1;
        $game->status = 'finished';

        if (!$this->Games->save($game)) {
            debug($game->getErrors());
            die;
        }
    }

    if (!$this->Games->UsersInGames->save($player)) {
        debug($player->getErrors());
        die;
    }

    return $this->redirect(['action' => 'playLabyrinth', $id]);
}

}