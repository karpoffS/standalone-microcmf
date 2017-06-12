<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 03.06.17
 * Time: 17:05
 */

/**
 * Регистрируем роутер
 */
$router = new Pux\Mux();

//$default_route = [
//    'options' => [
//        'constructor_args' => [ &$container ],
//    ]
//];
//
//$routes = [
//    [
//        'pattern' => "/",
//        'controller' => \App\Controller\PageController::class,
//        'action' => "indexAction",
//        'options' => [
//            'constructor_args' => [ &$container ],
//        ]
//    ],
//    [
//        'pattern' => "/first",
//        'controller' => \App\Controller\PageController::class,
//        'action' => "firstAction",
//        'options' => [
//            'constructor_args' => [ &$container ],
//        ]
//    ],
//    [
//        'pattern' => "/second",
//        'controller' => \App\Controller\PageController::class,
//        'action' => "secondAction",
//        'options' => [
//            'constructor_args' => [ &$container ],
//        ]
//    ],
//
//    [
//        'pattern' => "/notfound",
//        'controller' => \App\Controller\ErrorsController::class,
//        'action' => "error404Action",
//        'options' => [
//            'constructor_args' => [ &$container ],
//        ]
//    ],
//];
//
//foreach ($routes as $route) {
//
//    if(empty($route['options']))
//        $route['options'] = $default_route['options'];
//
//    if (!empty($route['options']) && is_array($route['options']))
//        $route['options'] =
//            array_merge_recursive($default_route['options'], $route['options']);
//
//    $router->get(
//        $route['pattern'],
//        [ $route['controller'],$route['action']], // callback
//        $route['options'] // options
//    );
//}

//
//$router->get('/', [ \App\Controller\PageController::class,'indexAction'], [
//    'constructor_args' => [ &$container ],
//]);
//
//$router->get('/first', [ \App\Controller\PageController::class,'firstAction'], [
//    'constructor_args' => [ &$container ],
//]);
//
//$router->get('/second', [ \App\Controller\PageController::class, 'secondAction'], [
//    'constructor_args' => [ &$container ],
//]);

$router->mount('', (new \App\Controller\PageController($container))->expand(), [
    'constructor_args' => [ &$container ],
]);

$router->mount('', (new \App\Controller\ErrorsController($container))->expand(), [
    'constructor_args' => [ &$container ],
]);

//$router->any('/product/:id', [\App\Controller\PageController::class,'exampleAction'], [
//    'constructor_args' => [ &$container ],
//    'require' => [ 'id' => '\d+', ],
//    'default' => [ 'id' => '1', ]
//]);

$match = $router->dispatch($_SERVER["REQUEST_URI"]);

// Если не найдена страница
if (!$match) {
    $match = $router->dispatch('/notfound');
}

// Запускаем маршрут
$response = Pux\Executor::execute($match);

// Отвечаем клиенту
$container->get('emitter')->emit($response);

