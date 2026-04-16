<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class LabyrinthSettingsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('labyrinth_settings');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Games', [
            'foreignKey' => 'game_id',
            'joinType' => 'INNER'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('game_id')
            ->requirePresence('game_id', 'create')
            ->notEmptyString('game_id');

        $validator
            ->scalar('map_data')
            ->notEmptyString('map_data');

        $validator
            ->integer('treasure_x')
            ->notEmptyString('treasure_x');

        $validator
            ->integer('treasure_y')
            ->notEmptyString('treasure_y');

        return $validator;
    }
}