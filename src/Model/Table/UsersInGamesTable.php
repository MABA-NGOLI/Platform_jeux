<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class UsersInGamesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // Table SQL
        $this->setTable('users_in_games');

        // Primary key
        $this->setPrimaryKey('id');

        // Timestamp created/modified
        $this->addBehavior('Timestamp');

        // =========================
        // RELATIONS
        // =========================

        // Joueur
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);

        // Partie
        $this->belongsTo('Games', [
            'foreignKey' => 'game_id',
            'joinType' => 'INNER'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            // user_id obligatoire
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id')

            // game_id obligatoire
            ->integer('game_id')
            ->requirePresence('game_id', 'create')
            ->notEmptyString('game_id')

            // score = nombre de coups (Mastermind)
            ->integer('score')
            ->greaterThanOrEqual('score', 0)
            ->allowEmptyString('score', 0)

            // attempts = essais (optionnel mais utile)
            ->integer('attempts')
            ->greaterThanOrEqual('attempts', 0)
            ->allowEmptyString('attempts', 0);

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        // Vérifie que user existe
        $rules->add($rules->existsIn(['user_id'], 'Users'), [
            'errorField' => 'user_id'
        ]);

        // Vérifie que game existe
        $rules->add($rules->existsIn(['game_id'], 'Games'), [
            'errorField' => 'game_id'
        ]);

        // Empêche doublon user + game
        $rules->add($rules->isUnique(
            ['user_id', 'game_id'],
            'Ce joueur est déjà inscrit dans cette partie'
        ));

        return $rules;
    }
}
