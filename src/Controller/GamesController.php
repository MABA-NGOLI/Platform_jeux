<?php
declare(strict_types=1);

namespace App\Controller;

class GamesController extends AppController
{
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
            'controller' => 'Games',
            'action' => 'playMastermind',
            $game->id
        ]);
    }

    public function playMastermind($id)
    {
        $user = $this->request->getSession()->read('User');

        if (!$user) {
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        if (!$id) {
            throw new \Exception("ID manquant !");
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

        $lastFinishedGame = $this->Games->UsersInGames
            ->find()
            ->contain(['Games'])
            ->where([
                'UsersInGames.user_id' => $user->id,
                'Games.name' => 'mastermind',
                'Games.status' => 'finished'
            ])
            ->order(['Games.id' => 'DESC'])
            ->first();

        $message = null;

        if ($this->request->is('post')) {
            $guess = (string)$this->request->getData('guess');

            $userGame->attempts++;
            $userGame->score++;

            $message = $this->check($guess, $game->secret_code);

            if ($guess === $game->secret_code) {
                $game->status = 'finished';
                $this->Games->save($game);

                $message = "🎉 Bravo terminé en {$userGame->attempts} essais";
            }

            $this->Games->UsersInGames->save($userGame);

            $lastFinishedGame = $this->Games->UsersInGames
                ->find()
                ->contain(['Games'])
                ->where([
                    'UsersInGames.user_id' => $user->id,
                    'Games.name' => 'mastermind',
                    'Games.status' => 'finished'
                ])
                ->order(['Games.id' => 'DESC'])
                ->first();
        }

        $this->set(compact('game', 'userGame', 'message', 'lastFinishedGame'));
    }

    private function check($guess, $secret)
    {
        $correct = 0;
        $misplaced = 0;

        $g = str_split($guess);
        $s = str_split($secret);

        foreach ($g as $i => $v) {
            if (isset($s[$i]) && $v == $s[$i]) {
                $correct++;
                unset($s[$i], $g[$i]);
            }
        }

        foreach ($g as $v) {
            if (in_array($v, $s)) {
                $misplaced++;
                unset($s[array_search($v, $s)]);
            }
        }

        return "$correct bien placés / $misplaced mal placés";
    }


    //Jeu de Filler 

public function startFiller()
{
    $user = $this->request->getSession()->read('User');

    if (!$user) {
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    $size = 8;
    $colors = ['red', 'blue', 'green', 'yellow', 'purple', 'orange'];

    $board = [];
    for ($r = 0; $r < $size; $r++) {
        $row = [];
        for ($c = 0; $c < $size; $c++) {
            $row[] = [
                'color' => $colors[array_rand($colors)],
                'owner' => null
            ];
        }
        $board[] = $row;
    }

    $board[0][0]['owner'] = 1;
    $board[$size - 1][$size - 1]['owner'] = 2;

    $playerOneColor = $board[0][0]['color'];
    $playerTwoColor = $board[$size - 1][$size - 1]['color'];

    if ($playerOneColor === $playerTwoColor) {
        foreach ($colors as $color) {
            if ($color !== $playerOneColor) {
                $board[$size - 1][$size - 1]['color'] = $color;
                $playerTwoColor = $color;
                break;
            }
        }
    }

    $game = $this->Games->newEmptyEntity();
    $game->name = 'filler';
    $game->status = 'waiting';
    $game->secret_code = null;

    if (!$this->Games->save($game)) {
        debug($game->getErrors());
        die;
    }

    $settings = $this->Games->FillerSettings->newEmptyEntity();
    $settings->game_id = $game->id;
    $settings->board_size = $size;
    $settings->color_count = count($colors);
    $settings->grid_type = 'square';
    $settings->board_data = json_encode($board);
    $settings->player_one_id = $user->id;
    $settings->player_two_id = null;
    $settings->current_turn_user_id = $user->id;
    $settings->player_one_color = $playerOneColor;
    $settings->player_two_color = $playerTwoColor;
    $settings->winner_user_id = null;

    if (!$this->Games->FillerSettings->save($settings)) {
        debug($settings->getErrors());
        die;
    }

    $userGame = $this->Games->UsersInGames->newEmptyEntity();
    $userGame->user_id = $user->id;
    $userGame->game_id = $game->id;
    $userGame->score = 1;
    $userGame->attempts = 0;

    if (!$this->Games->UsersInGames->save($userGame)) {
        debug($userGame->getErrors());
        die;
    }

    return $this->redirect([
        'controller' => 'Games',
        'action' => 'playFiller',
        $game->id
    ]);
}

public function joinFiller($id)
{
    $user = $this->request->getSession()->read('User');

    if (!$user) {
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    $game = $this->Games->get($id, contain: ['FillerSettings']);
    $settings = $game->fillersettings;

    if ($game->name !== 'filler') {
        $this->Flash->error('Cette partie n’est pas une partie de Filler.');
        return $this->redirect('/');
    }

    if (!$settings) {
        $this->Flash->error('Paramètres Filler introuvables.');
        return $this->redirect('/');
    }

    if ($settings->player_two_id) {
        $this->Flash->error('La partie est déjà complète.');
        return $this->redirect('/filler/play/' . $id);
    }

    if ((int)$settings->player_one_id === (int)$user->id) {
        return $this->redirect('/filler/play/' . $id);
    }

    $settings->player_two_id = $user->id;
    $game->status = 'playing';

    if (!$this->Games->save($game)) {
        debug($game->getErrors());
        die;
    }

    if (!$this->Games->FillerSettings->save($settings)) {
        debug($settings->getErrors());
        die;
    }

    $userGame = $this->Games->UsersInGames->newEmptyEntity();
    $userGame->user_id = $user->id;
    $userGame->game_id = $game->id;
    $userGame->score = 1;
    $userGame->attempts = 0;

    if (!$this->Games->UsersInGames->save($userGame)) {
        debug($userGame->getErrors());
        die;
    }

    return $this->redirect('/filler/play/' . $id);
}

public function playFiller($id)
{
    $user = $this->request->getSession()->read('User');

    if (!$user) {
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    $game = $this->Games->get($id, contain: ['FillerSettings']);

    if (!$game->fillersettings) {
        $this->Flash->error('Paramètres Filler introuvables.');
        return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }

    $settings = $game->fillersettings;
    $board = json_decode((string)$settings->board_data, true);

    $isPlayerOne = (int)$settings->player_one_id === (int)$user->id;
    $isPlayerTwo = (int)$settings->player_two_id === (int)$user->id;

    if (!$isPlayerOne && !$isPlayerTwo) {
        $this->Flash->error('Vous ne faites pas partie de cette partie.');
        return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }

    $availableColors = $this->getAvailableFillerColors(
        $isPlayerOne ? $settings->player_one_color : $settings->player_two_color,
        $isPlayerOne ? $settings->player_two_color : $settings->player_one_color
    );

    $playerOneScore = $this->countFillerOwnedCells($board, 1);
    $playerTwoScore = $this->countFillerOwnedCells($board, 2);

    $joinLink = null;
    if (!$settings->player_two_id) {
        $joinLink = $this->request->getAttribute('webroot') . 'filler/join/' . $game->id;
    }

    $this->set(compact(
        'game',
        'settings',
        'board',
        'availableColors',
        'playerOneScore',
        'playerTwoScore',
        'isPlayerOne',
        'isPlayerTwo',
        'joinLink'
    ));
}

public function chooseFillerColor($id, $color)
{
    $user = $this->request->getSession()->read('User');

    if (!$user) {
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    $game = $this->Games->get($id, contain: ['FillerSettings']);

    if (!$game->fillersettings) {
        return $this->redirect(['action' => 'playFiller', $id]);
    }

    $settings = $game->fillersettings;

    if ($game->status !== 'playing') {
        return $this->redirect(['action' => 'playFiller', $id]);
    }

    if ((int)$settings->current_turn_user_id !== (int)$user->id) {
        $this->Flash->error("Ce n'est pas votre tour.");
        return $this->redirect(['action' => 'playFiller', $id]);
    }

    $isPlayerOne = (int)$settings->player_one_id === (int)$user->id;
    $isPlayerTwo = (int)$settings->player_two_id === (int)$user->id;

    if (!$isPlayerOne && !$isPlayerTwo) {
        return $this->redirect(['action' => 'playFiller', $id]);
    }

    $myColor = $isPlayerOne ? $settings->player_one_color : $settings->player_two_color;
    $opponentColor = $isPlayerOne ? $settings->player_two_color : $settings->player_one_color;

    if ($color === $myColor || $color === $opponentColor) {
        $this->Flash->error('Couleur interdite.');
        return $this->redirect(['action' => 'playFiller', $id]);
    }

    $board = json_decode((string)$settings->board_data, true);
    $owner = $isPlayerOne ? 1 : 2;

    $board = $this->applyFillerMove($board, $owner, $color);

    if ($isPlayerOne) {
        $settings->player_one_color = $color;
        $settings->current_turn_user_id = $settings->player_two_id;
    } else {
        $settings->player_two_color = $color;
        $settings->current_turn_user_id = $settings->player_one_id;
    }

    $settings->board_data = json_encode($board);

    $playerOneScore = $this->countFillerOwnedCells($board, 1);
    $playerTwoScore = $this->countFillerOwnedCells($board, 2);
    $total = count($board) * count($board[0]);

    if (($playerOneScore + $playerTwoScore) >= $total) {
        $game->status = 'finished';

        if ($playerOneScore > $playerTwoScore) {
            $settings->winner_user_id = $settings->player_one_id;
        } elseif ($playerTwoScore > $playerOneScore) {
            $settings->winner_user_id = $settings->player_two_id;
        } else {
            $settings->winner_user_id = null;
        }
    }

    if (!$this->Games->save($game)) {
        debug($game->getErrors());
        die;
    }

    if (!$this->Games->FillerSettings->save($settings)) {
        debug($settings->getErrors());
        die;
    }

    $playerOneUserGame = $this->Games->UsersInGames->find()
        ->where([
            'user_id' => $settings->player_one_id,
            'game_id' => $game->id
        ])
        ->first();

    if ($playerOneUserGame) {
        $playerOneUserGame->score = $playerOneScore;
        $this->Games->UsersInGames->save($playerOneUserGame);
    }

    if ($settings->player_two_id) {
        $playerTwoUserGame = $this->Games->UsersInGames->find()
            ->where([
                'user_id' => $settings->player_two_id,
                'game_id' => $game->id
            ])
            ->first();

        if ($playerTwoUserGame) {
            $playerTwoUserGame->score = $playerTwoScore;
            $this->Games->UsersInGames->save($playerTwoUserGame);
        }
    }

    return $this->redirect(['action' => 'playFiller', $id]);
}

private function getAvailableFillerColors(?string $myColor, ?string $opponentColor): array
{
    $colors = ['red', 'blue', 'green', 'yellow', 'purple', 'orange'];

    return array_values(array_filter($colors, function ($color) use ($myColor, $opponentColor) {
        return $color !== $myColor && $color !== $opponentColor;
    }));
}

private function countFillerOwnedCells(array $board, int $owner): int
{
    $count = 0;

    foreach ($board as $row) {
        foreach ($row as $cell) {
            if (($cell['owner'] ?? null) === $owner) {
                $count++;
            }
        }
    }

    return $count;
}

private function applyFillerMove(array $board, int $owner, string $newColor): array
{
    $rows = count($board);
    $cols = count($board[0]);

    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if (($board[$r][$c]['owner'] ?? null) === $owner) {
                $board[$r][$c]['color'] = $newColor;
            }
        }
    }

    $changed = true;

    while ($changed) {
        $changed = false;

        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                if (($board[$r][$c]['owner'] ?? null) !== null) {
                    continue;
                }

                if (($board[$r][$c]['color'] ?? null) !== $newColor) {
                    continue;
                }

                $neighbors = [
                    [$r - 1, $c],
                    [$r + 1, $c],
                    [$r, $c - 1],
                    [$r, $c + 1],
                ];

                foreach ($neighbors as [$nr, $nc]) {
                    if ($nr < 0 || $nc < 0 || $nr >= $rows || $nc >= $cols) {
                        continue;
                    }

                    if (($board[$nr][$nc]['owner'] ?? null) === $owner) {
                        $board[$r][$c]['owner'] = $owner;
                        $changed = true;
                        break;
                    }
                }
            }
        }
    }

    return $board;
}

}