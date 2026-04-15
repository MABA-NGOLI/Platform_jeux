<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersInGamesFixture
 */
class UsersInGamesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'user_id' => 1,
                'game_id' => 1,
                'score' => 1,
                'created' => '2026-04-15 17:16:18',
                'modified' => '2026-04-15 17:16:18',
            ],
        ];
        parent::init();
    }
}
