<?php

namespace models;

use components\DataBase as DB;

class Contacts {

    /**
     * Добавить новый контакт
     *
     * @param   string  $name           имя
     * @param   int     $phone_number   телефон
     *
     * @return  int
     */
    public static function create(
        string  $name,
        int     $phone_number
    ): int {
        $name = substr($name, 0, 64);
        $data = [
            'name' => $name,
            'phone_number' => $phone_number,
        ];

        return (int) DB::_insert('contacts', $data, 'id');
    }


    /**
     * Удалить контакт
     *
     * @param   int     $contact_id    id контакта
     *
     * @return  bool
     */
    public static function delete(
        int     $contact_id
    ): bool {
        $wheres = [
            'id' => $contact_id
        ];

        return (bool) DB::_delete('contacts', $wheres);
    }


    /**
     * Получить все контакты
     *
     * @return  array   массив контактов
     */
    public static function getAll(): array {
        $sql = "
            SELECT *
            FROM `contacts`
            WHERE 1
        ";

        $res = DB::_exc_sql('contacts', $sql, [], DB::FETCH);

        return $res;
    }
}