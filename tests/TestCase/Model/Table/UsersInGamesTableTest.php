<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersInGamesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersInGamesTable Test Case
 */
class UsersInGamesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersInGamesTable
     */
    protected $UsersInGames;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.UsersInGames',
        'app.Users',
        'app.Games',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('UsersInGames') ? [] : ['className' => UsersInGamesTable::class];
        $this->UsersInGames = $this->getTableLocator()->get('UsersInGames', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->UsersInGames);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\UsersInGamesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\UsersInGamesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
