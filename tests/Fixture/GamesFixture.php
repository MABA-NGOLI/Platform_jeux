<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * GamesFixture
 */
class GamesFixture extends TestFixture
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
                'category_id' => 1,
                'title' => 'Lorem ipsum dolor sit amet',
                'slug' => 'Lorem ipsum dolor sit amet',
                'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'platform' => 'Lorem ipsum dolor sit amet',
                'genre' => 'Lorem ipsum dolor sit amet',
                'release_year' => 1,
                'max_players' => 1,
                'image' => 'Lorem ipsum dolor sit amet',
                'is_featured' => 1,
                'created' => '2026-04-15 16:49:09',
                'modified' => '2026-04-15 16:49:09',
            ],
        ];
        parent::init();
    }
}
