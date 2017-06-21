<?php

/**
 * Маршруты
 */
return [
    [
        'method' => "get",
        'pattern' => "/",
        'controller' => \App\Controller\PageController::class,
        'action' => "indexAction",
    ],
    [
        'method' => "get",
        'pattern' => "/first",
        'controller' => \App\Controller\PageController::class,
        'action' => "firstAction",
    ],
    [
        'method' => "get",
        'pattern' => "/second",
        'controller' => \App\Controller\PageController::class,
        'action' => "secondAction",
    ],

    [
        'method' => "any",
        'pattern' => "/notfound",
        'controller' => \App\Controller\ErrorsController::class,
        'action' => "error404Action",
    ],
];