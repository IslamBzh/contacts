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

exit();

/*
    Т.к. сайт состоит из одной станицы и В ТЗ не указано ничего о структуре бэка:
        роутера не будет. просто запустим единственный контроллер - `main` действие - `index`
 */

$model  = 'contacts';
$action = 'index';
$params = [];

$controller_class   = 'Controllers\\' . ucfirst($model);
$controller_object  = new $controller_class();

$controller_method  = 'action' . ucfirst($action);

$data = call_user_func_array([$controller_object, $controller_method], $params);

if(!is_array($data))
    $data = [];

/*
    У нас всего одна страница. Так что запустим ее
 */

$tm = new components\TemplateMaster($model, $action, $data);
$tm->print();