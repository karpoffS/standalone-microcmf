<?php
ini_set('display_errors', 'On');

// Подключаем автозагрузчик классов composer
require_once __DIR__ . DS . '../vendor/autoload.php';

if(getenv("APP_DEBUG") && !defined('APP_DEBUG'))
    define('APP_DEBUG', getenv("APP_DEBUG"));

if(!getenv("APP_DEBUG") && !defined('APP_DEBUG'))
    define('APP_DEBUG', false);


if(APP_DEBUG){
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

// Путь к приложению
define('BASE_PATH', __DIR__ . DS);
//define('BASE_PATH', dirname(dirname(__DIR__)) . DS);

// Путь к папке app
define('APP_PATH', __DIR__ . DS);

// Путь к конфигу
define('APP_CONFIG', APP_PATH . 'config'.DS);

// Путь к папке кеша
define('APP_CACHE', APP_PATH . 'cache'.DS);

// Путь к папке кешей для вьюшек
define('VIEWS_CACHE', APP_CACHE . 'views'.DS);

// Путь к папке логов
define('APP_LOGS', APP_PATH . 'logs'.DS);

// Путь к папке шаблонов
define('APP_VIEWS', APP_PATH . 'views'.DS);

// Путь к папке исходников
define('SRC_PATH', APP_PATH . 'src'.DS);

/**
 * Регистрируем контейнер зависимостей
 */
$container = require_once "container.php";

// Get timezone
$timezone = $container->get('parameters')->get("config")->app->timezone;

// Устанавливаем временную зону
date_default_timezone_set($timezone);

/**
 * Регистрируем Маршруты
 */
require_once "routing.php";

