<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

Router::plugin('NifaAppsManager', function ($routes) {
        //$routes->connect('/login', ['plugin' => 'NifaAuth', 'controller' => 'Users', 'action' => 'login']);
        $routes->fallbacks('DashedRoute');
    });


/*Router::prefix('admin', function ($routes) {
    // All routes here will be prefixed with `/admin`
    // And have the prefix => admin route element added.
    $routes->connect('/applications/:action', ['plugin' => 'NifaAppsManager', 'controller' => 'Users']);
    $routes->fallbacks(DashedRoute::class);
});*/