<?php

// Объявляем сокращённую форму DIRECTORY_SEPARATOR
if(!defined('DS')){
    define("DS", DIRECTORY_SEPARATOR);
}

define('APP_DEBUG', true);

// Подключаем настройки
require_once '..'.DS.'app'.DS.'bootstrap.php';
