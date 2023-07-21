<?php

// Время начало загрузки
define('L_TIME', time());

// Основная дирректория
define('ROOT_DIR', __DIR__);

define('CONTROLLERS_DIR', __DIR__ . '/controllers');
define('CONFIG_DIR', __DIR__ . '/config');


// простой авто-загрузчик
spl_autoload_register(function($class) {

    $path = ROOT_DIR . '/' . $class . '.php';
    $path = str_replace('\\', '/', $path);

    if(!file_exists($path)){
        trigger_error("Class \"" . $class . "\" not exist!\n\nFILE PATH: " . $path, E_USER_WARNING);
        return false;
    }

    require_once $path;
});

new components\Router();
