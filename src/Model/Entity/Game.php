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
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'category_id' => true,
        'title' => true,
        'slug' => true,
        'description' => true,
        'platform' => true,
        'genre' => true,
        'release_year' => true,
        'max_players' => true,
        'image' => true,
        'is_featured' => true,
        'created' => true,
        'modified' => true,
        'category' => true,
        'reviews' => true,
    ];
}
