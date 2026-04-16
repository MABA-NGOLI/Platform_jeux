<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class GamesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('games');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // relation avec users_in_games (scores)
        $this->hasMany('UsersInGames', [
            'foreignKey' => 'game_id',
            'dependent' => true
        ]);

        // relation users 
        $this->belongsToMany('Users', [
            'through' => 'UsersInGames'
        ]);

        //relation fillersettings

        $this->hasOne('FillerSettings', [
            'foreignKey' => 'game_id',
            'dependent' => true,
            'propertyName' => 'fillersettings'
        ]);

        //relation boardgames
        $this->belongsTo('BoardGames', [
            'foreignKey' => 'board_game_id'
        ]);
        

        //relation labyrinthsettings
        $this->hasOne('LabyrinthSettings', [
            'foreignKey' => 'game_id',
            'dependent' => true,
            'propertyName' => 'labyrinth_settings'
    ]);
    }


    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name')

            ->scalar('status')
            ->inList('status', ['waiting', 'playing', 'finished'])
            ->notEmptyString('status')

            ->scalar('secret_code')
            ->maxLength('secret_code', 10)
            ->allowEmptyString('secret_code');

        return $validator;
    }
}
