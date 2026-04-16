<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
class UsersInGame extends Entity
{
   
    protected array $_accessible = [
        'user_id' => true,
        'game_id' => true,
        'score' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'game' => true,
    ];
}
