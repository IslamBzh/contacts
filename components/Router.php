<?php

namespace components;

use Config;

use components\TemplateMaster as TM;

use components\Session;

class Router extends Request{

    public static   $module;

    public static   $action;

    public static   $segments;

    public static   $route          = null;

    public static   $only_auth      = false;

    public static   $is_request     = false;

    private static  $controller_path;

    private static  $controller_class;


    function __construct(){
        self::initializationRequest();

        Session::start();

        self::$segments = $this->getSegments();

        self::$module = array_shift(self::$segments);
        self::$action = array_shift(self::$segments);

        $controller_object = $this->initializationController();
        $controller_method = $this->initializationAction($controller_object, self::$segments);

        $res = $this->run($controller_object, $controller_method, self::$segments);

        $tm = new TM(self::$module, self::$action, $res);

        $tm->print();
    }


    /**
     * Обработка запроса, роутер
     *
     * @return array  Массив сигментов
     */
    private function getSegments(): array {
        $routes = require CONFIG_DIR . '/routes.php';

        $newPath = null;
        foreach ($routes as $uriPattern => $patternDetails) {
            if (preg_match("~^$uriPattern$~", self::$url)) {

                $route = $patternDetails;

                $newPath = preg_replace("~$uriPattern~", $route, self::$url);

                if(strpos($newPath, ':') !== false)
                    System::redirect($newPath);

                break;
            }
        }

        if(empty($newPath)){
            echo "<pre>";
            var_dump(self::$url, $routes);
            echo "</pre>";
        }

        $segments = explode('/', $newPath);

        $segments[0] = !empty($segments[0])
            ? strtolower($segments[0])
            : 'main';

        $segments[1] = !empty($segments[1])
            ? strtolower($segments[1])
            : 'index';

        return $segments;
    }

    /**
     * Запуск контроллера
     * После, запуск фронт страницы (см __construct)
     *
     * @param  object $controller_object Объект контроллера
     * @param  string $controller_method Метод действия
     * @param  array  $params            Параметры
     *
     * @return array  данные для рендера страницы
     */
    private function run(
        object $controller_object,
        string $controller_method,
        array  $params
    ): array {
        $data = call_user_func_array([$controller_object, $controller_method], $params);

        if(!is_array($data))
            $data = [];

        return $data;
    }

    /**
     * Инициализация контроллера
     *
     * @return object Объект контроллера
     */
    private function initializationController() {
        $module = ucfirst(self::$module);

        self::$controller_path = CONTROLLERS_DIR . '/' . $module . '.php';

        if(!file_exists(self::$controller_path))
            self::ErrorPage('Controller file not found.', [
                'controller_path' => self::$controller_path
            ]);

        require_once self::$controller_path;

        self::$controller_class = 'controllers\\' . $module;

        if(!class_exists(self::$controller_class))
            self::ErrorPage('Controller Class not found.', [
                'controller_path' => self::$controller_path,
                'class' => self::$controller_class
            ]);

        $controller_object = new self::$controller_class();

        return $controller_object;
    }


    /**
     * Инициализация действия
     *
     * @return string название класса контроллера
     */
    private function initializationAction(object $controller_object): string {

        $controller_method = 'action' . ucfirst(self::$action);

        if(!method_exists($controller_object, $controller_method))
            self::ErrorPage('Controller Method not found.', [
                'controller_path' => self::$controller_path,
                'method' => self::$controller_class . '->' . $controller_method . '();'
            ]);

        return $controller_method;
    }

    public static function ErrorPage($message = '', $result = '', int $code = 404){
        echo "<pre>";
        var_dump(func_get_args());
        echo "</pre>";

        if(self::$is_request)
            return Responder\Json::pre(
                'req',
                $message,
                $result
            );

        if($code == 404)
            TM::return404([
                'message' => $message,
                'result' => $result
            ], $code);

        if($code == 403)
            TM::return403([
                'imoon'  => 'cogs',
                'message' => $message,
                'result' => $result
            ], $code);

        exit();
    }
}