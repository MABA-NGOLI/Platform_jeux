<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {

    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {

        //Home page
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

       // auth routes
        $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        $builder->connect('/register', ['controller' => 'Users', 'action' => 'register']);
        $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
        $builder->connect('/profile', ['controller' => 'Users', 'action' => 'profile']);

       // Mastermind

       $builder->connect('/mastermind/start', [
        'controller' => 'Mastermind',
        'action' => 'startMastermind'
      ]);

        $builder->connect('/mastermind/play/{id}', [
        'controller' => 'Mastermind',
        'action' => 'playMastermind'
        ])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);


       //Filler
    
        $builder->connect('/filler/start', [
        'controller' => 'Filler',
        'action' => 'startFiller'
        ]);

        $builder->connect('/filler/join/{id}', [
            'controller' => 'Filler',
            'action' => 'joinFiller'
        ])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);

        $builder->connect('/filler/play/{id}', [
            'controller' => 'Filler',
            'action' => 'playFiller'
        ])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);

        $builder->connect('/filler/choose/{id}/{color}', [
            'controller' => 'Filler',
            'action' => 'chooseFillerColor'
        ])
        ->setPatterns([
            'id' => '\d+',
            'color' => '[a-z]+'
        ])
        ->setPass(['id', 'color']);



      // Labyrinthe

        $builder->connect('/labyrinth/start', [
        'controller' => 'Games',
        'action' => 'startLabyrinth'
      ]);

        $builder->connect('/labyrinth/join/{id}', [
            'controller' => 'Games',
            'action' => 'joinLabyrinth'
        ])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);

        $builder->connect('/labyrinth/play/{id}', [
            'controller' => 'Games',
            'action' => 'playLabyrinth'
        ])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);

        $builder->connect('/labyrinth/move/{id}/{direction}', [
            'controller' => 'Games',
            'action' => 'moveLabyrinth'
        ])
        ->setPatterns([
            'id' => '\d+',
            'direction' => 'up|down|left|right'
        ])
        ->setPass(['id', 'direction']);


        // fallback (TOUJOURS DERNIER)
        $builder->fallbacks();
    });
};
