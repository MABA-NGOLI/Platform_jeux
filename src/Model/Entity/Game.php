<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Game Entity
 *
 * @property int $id
 * @property int $category_id
 * @property string $title
 * @property string|null $slug
 * @property string $description
 * @property string $platform
 * @property string $genre
 * @property int $release_year
 * @property int $max_players
 * @property string|null $image
 * @property bool $is_featured
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Category $category
 * @property \App\Model\Entity\Review[] $reviews
 */
class Game extends Entity
{
  
    protected array $_accessible = [
        
        'title' => true,
        
    ];
}
