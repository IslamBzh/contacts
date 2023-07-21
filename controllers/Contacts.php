<?php

namespace controllers;

use models\contacts as Model;

use components\Request;
use components\Session;

class Contacts {

    public function actionIndex(){

        $contacts = Model::getAll();

        // echo "<pre>";
        // var_dump($contacts);
        // exit;

        return [];
    }

    public function actionApi(string $action){
        if(!Session::checkCSRF(Request::HEADERS('X-CSRF-Token')))
            return [
                'action'    => $action,
                'status'    => false,
                'error'     => 'CSRF token'
            ];

        $status = false;
        $result = [];

        switch ($action) {
            case 'create':
                $data   = Request::POST('name', 'phone_number');
                $data['phone_number'] = str_replace([' ', '+'], '', $data['phone_number']);

                $res    = Model::create($data['name'], $data['phone_number']);

                if($res){
                    $status = true;
                    $result = $data + ['id' => $res];
                }
                break;

            case 'delete':
                $res = Model::delete(Request::POST('contact_id'));

                if($res)
                    $status = true;
                break;

            case 'getAll':
                $res    = Model::getAll();

                if($res){
                    $status = true;
                    $result = $res;
                }
                break;
        }

        return [
            'action'    => $action,
            'status'    => $status,
            'result'    => $result,
            'POST'      => Request::POST()
        ];
    }

}