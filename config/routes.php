<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {

    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {

        // =========================
        // HOME
        // =========================
        $builder->connect('/', [
            'controller' => 'Pages',
            'action' => 'display',
            'home'
        ]);

        // =========================
        // PAGES
        // =========================
        $builder->connect('/pages/*', [
            'controller' => 'Pages',
            'action' => 'display'
        ]);

        // =========================
        // AUTH
        // =========================
        $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        $builder->connect('/register', ['controller' => 'Users', 'action' => 'register']);
        $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
        $builder->connect('/profile', ['controller' => 'Users', 'action' => 'profile']);

        // =========================
        // JEUX (MASTERMIND)
        // =========================

       $builder->connect('/mastermind/start', [
    'controller' => 'Games',
        'action' => 'startMastermind'
    ]);

    $builder->connect('/mastermind/play/{id}', [
    'controller' => 'Games',
    'action' => 'playMastermind'
    ])
    ->setPatterns(['id' => '\d+'])
    ->setPass(['id']);



    //Filler
    $builder->connect('/filler/start', [
    'controller' => 'Games',
    'action' => 'startFiller'
    ]);

    $builder->connect('/filler/join/{id}', [
        'controller' => 'Games',
        'action' => 'joinFiller'
    ])
    ->setPatterns(['id' => '\d+'])
    ->setPass(['id']);

    $builder->connect('/filler/play/{id}', [
        'controller' => 'Games',
        'action' => 'playFiller'
    ])
    ->setPatterns(['id' => '\d+'])
    ->setPass(['id']);

    $builder->connect('/filler/choose/{id}/{color}', [
        'controller' => 'Games',
        'action' => 'chooseFillerColor'
    ])
    ->setPatterns([
        'id' => '\d+',
        'color' => '[a-z]+'
    ])
    ->setPass(['id', 'color']);


        // fallback (TOUJOURS DERNIER)
        $builder->fallbacks();
    });
};
