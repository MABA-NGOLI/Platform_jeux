<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class FillerSettingsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('fillersettings');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Games', [
            'foreignKey' => 'game_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('PlayerOne', [
            'className' => 'Users',
            'foreignKey' => 'player_one_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('PlayerTwo', [
            'className' => 'Users',
            'foreignKey' => 'player_two_id',
            'joinType' => 'LEFT'
        ]);

        $this->belongsTo('CurrentTurnUser', [
            'className' => 'Users',
            'foreignKey' => 'current_turn_user_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('WinnerUser', [
            'className' => 'Users',
            'foreignKey' => 'winner_user_id',
            'joinType' => 'LEFT'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('game_id')
            ->requirePresence('game_id', 'create')
            ->notEmptyString('game_id');

        $validator
            ->integer('board_size')
            ->greaterThanOrEqual('board_size', 4)
            ->notEmptyString('board_size');

        $validator
            ->integer('color_count')
            ->greaterThanOrEqual('color_count', 4)
            ->notEmptyString('color_count');

        $validator
            ->scalar('grid_type')
            ->notEmptyString('grid_type');

        $validator
            ->scalar('board_data')
            ->notEmptyString('board_data');

        return $validator;
    }
}