<?php

namespace components;

use config\db as config;

use PDO;

class DataBase {

    const FETCH     = 901;
    const END_KEY   = 902;

    public static $db   = null;
    public static $name = null;

    public static function Connect() {
        if(!self::$db){
            try {
                $host = config::HOST;
                $name = config::NAME;
                $user = config::USER;
                $pass = config::PASS;

                self::$db = new PDO(
                    "mysql:host=$host;" .
                    "dbname=$name;" .
                    "charset=utf8mb4",
                    $user,
                    $pass,
                    [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]
                );

                self::$name = $name;
            }

            catch (PDOException $e) {
                Errors::addMessage('PDO Connect error. ' . $e->getMessage(), [], true);
            }
        }

        return self::$db;
    }

    public static function execute(& $result, $sql = '', $bind = []){
        try {
            $res = $result->execute();
            return $res;
        }

        catch (\PDOException $e) {
            self::debug(__FILE__ . ':' . __LINE__ . "\n" . $e->getMessage(), $sql, $bind);
        }
    }

    public static function query(& $db, $sql = '', $bind = []){
        try {
            $res = $db->query($sql);

            return $res;
        }

        catch (\PDOException $e) {
            self::debug(__FILE__ . ':' . __LINE__ . "\n" . $e->getMessage(), $sql, $bind);
        }

        catch (\Exception $e) {
            self::debug(__FILE__ . ':' . __LINE__ . "\n" . $e->getMessage(), $sql, $bind);
        }
    }

    private static function debug($res, & $sql, & $bind){
        if(!empty($sql) && !empty($bind_prms)){
            $search = [];
            $replace = [];
            foreach ($bind_prms as $bind_prm) {
                array_push($search, ':' . $bind_prm[0]);
                array_push($replace, $bind_prm[1]);
            }

            str_replace($search, $replace, $sql);
        }

        $res .= "\n\nSQL:\n" . $sql;

        print("<pre>\nSQL ERROR:\n");
        print($res);
        print("</pre>");
        exit;
    }


    public static function _insert(
        string  $table_name,
        array   $data,
        string  $return_max_key = ''
    ){
        $keys   = array_keys($data);

        $table_name = '`' . $table_name . '`';
        $columns    = '`' . implode('`, `', $keys) . '`';
        $values     = ':' . implode(', :', $keys);

        $sql    = "INSERT INTO {$table_name}({$columns}) VALUES ({$values})";

        $cmd = empty($return_max_key) ? 0 : self::END_KEY;

        return self::_exc_sql($table_name, $sql, $data, $cmd, $return_max_key);
    }

    public static function _delete(
        string  $table_name,
        array   $where,
        bool    $and = true
    ){
        $keys   = array_keys($where);

        $wheres = [];
        foreach ($keys as $key)
            $wheres[] = $key . ' = :' . $key;

        $wheres = implode($and ? ' AND ' : ' OR ', $wheres);

        $sql    = "DELETE FROM {$table_name} WHERE {$wheres}";

        // return [$sql, $where];

        return self::_exc_sql($table_name, $sql, $where);
    }

    public static function _exc_sql(
        $table_name,
        $sql,
        $bind   = [],
        $cmd    = 0,
        $re_key = '',
        $arr_to_arr = false
    ){
        $db = self::Connect();

        $result = $db->prepare($sql);

        if(!empty($bind)){
            foreach ($bind as $bkey => $bvalue){
                if(is_array($bvalue) || is_object($bvalue))
                    $bvalue = serialize($bvalue);

                if($bvalue === null)
                    $result->bindValue(":$bkey", NULL, \PDO::PARAM_NULL);

                elseif(is_numeric($bvalue) || is_bool($bvalue)){
                    $bvalue = (int) $bvalue;
                    $result->bindValue(":$bkey", $bvalue, \PDO::PARAM_INT);
                }

                else
                    $result->bindValue(":$bkey", $bvalue, \PDO::PARAM_STR);
            }
        }

        $res = self::execute($result, $sql, $bind);

        if($cmd == self::FETCH)
            return self::SelectFetch($result, $re_key, $arr_to_arr);

        if($res && $cmd == self::END_KEY)
            return self::_get_max_key($table_name, $re_key);

        return $res;
    }

    public static function _get_max_key(
        $table_name,
        $key
    ){
        $db = self::Connect();
        $sql = "
            SELECT MAX({$key}) as max FROM {$table_name};
        ";

        $result = $db->query($sql);

        $result = $result->fetch();

        return $result['max'];
    }


    public static function SelectFetch(
        $result,
        $sort_key = false,
        $arr_to_arr = false
    ){
        $id = 0;
        $datas = [];

        while($row = $result->fetch(\PDO::FETCH_ASSOC)) {

            if($sort_key && isset($row[$sort_key])){
                if(isset($row[$sort_key]))
                    $id = $row[$sort_key];
                else
                    $id = 'null';
            }

            else
                $sort_key = false;

            if($arr_to_arr)
                $datas[$id][] = $row;

            elseif(isset($datas[$id]) && ($arr_to_arr = true))
                $datas[$id] = [$datas[$id], $row];

            else
                $datas[$id] = $row;

            if(is_numeric($id))
                $id++;
        }

        if(empty($datas) || !is_array($datas))
            return [];

        return $datas;
    }
}
