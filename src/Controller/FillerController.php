<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Routing\Router;

class FillerController extends AppController
{
    protected $Games;

    public function initialize(): void
    {
        parent::initialize();
        $this->Games = $this->fetchTable('Games');
    }

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
            'controller' => 'Filler',
            'action' => 'playFiller',
            $game->id
        ]);


     $boardGame = $this->Games->BoardGames->find()
    ->where(['slug' => 'filler'])
    ->first();

    $game = $this->Games->newEmptyEntity();
    $game->name = 'filler';
    $game->board_game_id = $boardGame ? $boardGame->id : null;
    $game->status = 'waiting';
    $game->secret_code = null;

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
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }

        if (!$settings) {
            $this->Flash->error('Paramètres Filler introuvables.');
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }

        if ($settings->player_two_id) {
            $this->Flash->error('La partie est déjà complète.');
            return $this->redirect([
                'controller' => 'Filler',
                'action' => 'playFiller',
                $id
            ]);
        }

        if ((int)$settings->player_one_id === (int)$user->id) {
            return $this->redirect([
                'controller' => 'Filler',
                'action' => 'playFiller',
                $id
            ]);
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

        return $this->redirect([
            'controller' => 'Filler',
            'action' => 'playFiller',
            $id
        ]);
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
            $joinLink = Router::url([
                'controller' => 'Filler',
                'action' => 'joinFiller',
                $game->id
            ], true);
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
            return $this->redirect([
                'controller' => 'Filler',
                'action' => 'playFiller',
                $id
            ]);
        }

        $settings = $game->fillersettings;

        if ($game->status !== 'playing') {
            return $this->redirect([
                'controller' => 'Filler',
                'action' => 'playFiller',
                $id
            ]);
        }

        if ((int)$settings->current_turn_user_id !== (int)$user->id) {
            $this->Flash->error("Ce n'est pas votre tour.");
            return $this->redirect([
                'controller' => 'Filler',
                'action' => 'playFiller',
                $id
            ]);
        }

        $isPlayerOne = (int)$settings->player_one_id === (int)$user->id;
        $isPlayerTwo = (int)$settings->player_two_id === (int)$user->id;

        if (!$isPlayerOne && !$isPlayerTwo) {
            return $this->redirect([
                'controller' => 'Filler',
                'action' => 'playFiller',
                $id
            ]);
        }

        $myColor = $isPlayerOne ? $settings->player_one_color : $settings->player_two_color;
        $opponentColor = $isPlayerOne ? $settings->player_two_color : $settings->player_one_color;

        if ($color === $myColor || $color === $opponentColor) {
            $this->Flash->error('Couleur interdite.');
            return $this->redirect([
                'controller' => 'Filler',
                'action' => 'playFiller',
                $id
            ]);
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

        return $this->redirect([
            'controller' => 'Filler',
            'action' => 'playFiller',
            $id
        ]);
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