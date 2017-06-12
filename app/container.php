<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 07.06.17
 * Time: 16:51
 */


$container = new League\Container\Container;

/**
 * Загружаем конфиг
 *
 * @var \Core\Common\Config $config
 */
$container->share('parameters', function (){

    $files = glob(APP_CONFIG.'*.php', GLOB_BRACE);

    return (new \Core\Common\Config($files))();
});

/**
 * Сервис запросов
 */
$container->share('request', function () {
    return \Zend\Diactoros\ServerRequestFactory::fromGlobals(
        $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
    );
});

/**
 * Сервис ответов
 */
$container->share('response', \Zend\Diactoros\Response::class);

/**
 * Ответчик приложения
 */
$container->share('emitter', Zend\Diactoros\Response\SapiEmitter::class);


/**
 * PDO класс работы с БД
 */
$container->share('pdo', function () use (&$container){

    $setting = $container->get('parameters')->get('config')->db;

    $dsn = "mysql:host={$setting->host};dbname={$setting->name}";

    try {

        return new PDO($dsn,$setting->user, $setting->pass,
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.mb_strtoupper($setting->character)
            ]
        );
    } catch (\PDOException $e) {

        \Core\Library\Logger::log($e->getMessage(), 'db');
    }
});

/**
 * Регистрируем Класс работы с БД
 * Получать вот так $db = $container->get("db");
 */
$container->share( "db", function () use (&$container){

    /** @var \Core\Common\DB $db */
    $db = new \Core\Common\DB($container->get('pdo'));
    return $db;
});

/**
 * Подключаем сервис кеширования backend
 */
$container->share("cache", function (){

    $backends = [];

    if(class_exists("Redis")){
        $conn = new Redis();
        if($conn && $conn->connect("127.0.0.1", 6379, 3)){
            $backends[] = new UniversalCache\RedisCache($conn);
        }
    }

    // Подключаем универсальный кешер
    return new UniversalCache\UniversalCache($backends);

});

/**
 * Подключаем сервис кеширования frontend
 */
$container->share("cacheFront", function (){

    $backends = [];

    $dir = APP_CACHE.DIRECTORY_SEPARATOR."frontend".DIRECTORY_SEPARATOR;

    $backends[] = new UniversalCache\FileSystemCache($dir);

    // Подключаем универсальный кешер
    return new UniversalCache\UniversalCache($backends);

});

/**
 * Подключаем шаблонизатор Plates
 */
$container->share("views", function (){

    // Create new Plates instance
    $engine = new League\Plates\Engine(APP_VIEWS);

    $engine->setFileExtension("phtml");

    // Search folders
    $folders = glob(APP_VIEWS."*", GLOB_ONLYDIR);

    // Add folders
    foreach ($folders as $folder) {
        $engine->addFolder(basename($folder), $folder.DS);
    }

    // Load URI extension using global variable
    $engine->loadExtension(new \League\Plates\Extension\URI($_SERVER['REQUEST_URI']));

    return $engine;
});

return $container;