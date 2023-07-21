<?php

namespace components;

use Config;

class Request {

    protected static $uri;
    protected static $uri_no_utm;

    protected static $host;
    protected static $path;
    protected static $url;

    protected static $subdomain = '';

    protected static $GET;
    protected static $POST;
    protected static $FILES;
    protected static $COOKIE;
    protected static $HEADERS;

    protected static function initializationRequest(){
        self::$uri  = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $parse      = parse_url(self::$uri);
        self::$host = $parse['host'];
        self::$path = $parse['path'];
        selF::$url  = trim(self::$path, '/');

        self::$GET      = $_GET;
        self::$POST     = $_POST;
        self::$FILES    = $_FILES;
        self::$COOKIE   = $_COOKIE;
        self::$HEADERS  = apache_request_headers();
    }

    public static function isHTTPS(){

        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443;
    }

    public static function httpsCheck(){

        if(Config::ONLY_HTTPS && !self::isHTTPS()){

            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

            exit();
        }
    }

    public static function uri(){
        return self::$uri;
    }

    public static function host(){
        return self::$host;
    }

    public static function path(){
        return self::$path;
    }

    public static function url(){
        return self::$url;
    }

    public static function subdomain(){
        return self::$subdomain;
    }


    public static function GET(... $keys){

        return self::_getRequest(self::$GET, $keys);
    }

    public static function POST(... $keys){

        return self::_getRequest(self::$POST, $keys);
    }

    public static function FILES(... $keys){

        return self::_getRequest(self::$FILES, $keys);
    }

    public static function COOKIE(... $keys){

        return self::_getRequest(self::$COOKIE, $keys);
    }

    public static function HEADERS(... $keys){

        return self::_getRequest(self::$HEADERS, $keys);
    }

    private static function _getRequest($data, $keys){
        if(empty($data))
            return null;

        if(empty($keys))
            return $data;

        if(count($keys) == 1)
            return $data[$keys[0]] ?? null;

        return array_intersect_key($data, array_flip($keys));
    }


    public static function getDetails() {
        return [
            'URI'   => self::uri(),
            'GET'   => self::GET(),
            'POST'  => self::POST(),
            'FILES' => self::FILES(),
        ];
    }
}