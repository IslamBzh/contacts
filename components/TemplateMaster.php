<?php

namespace components;


/**
 * Простой загрузчик шаблона (html - контента)
 */
class TemplateMaster {

    public  $title      = '';

    public  $tmpl_path  = '';
    public  $data       = [];

    public  $is_api     = false;

    public function __construct(
        string  $model,
        string  $action,
        array   $data   = [],
        string  $title  = \config\web::SITE_NAME
    ){
        $this->title        = & $title;

        $this->tmpl_path    = ROOT_DIR . '/views/templates/' . $model . '/' . $action . '.phtml';
        $this->data         = & $data;

        if($action == 'api')
            $this->is_api = true;
    }


    /**
     * Установить данные, что необходимо передать
     * @param array $data [description]
     */
    public function setData(array $data){
        $this->data = $data;

        return $this;
    }


    public function setTitle(string $title){
        $this->title = $title;

        return $this;
    }


    public function print(){
        if($this->is_api)
            return $this->returnJson();

        if(!file_exists($this->tmpl_path))
            exit('File tmpl (' . $this->tmpl_path . ') not found!');

        require_once ROOT_DIR . '/views/layouts/index.php';
    }

    public function returnJson(){
        header('Content-Type: application/json');

        $result = json_encode($this->data,
            JSON_UNESCAPED_UNICODE  |
            JSON_UNESCAPED_SLASHES  |
            JSON_PRETTY_PRINT       |
            JSON_NUMERIC_CHECK
        );

        print($result);

        exit();
    }


    /**
     * Загрузит и вернет страницу
     */
    private function loadTmpl(){
        extract($this->data, EXTR_OVERWRITE);

        require $this->tmpl_path;
    }
}