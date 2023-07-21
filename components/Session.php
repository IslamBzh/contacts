<?php

namespace components;

// ini_set('session.cookie_httponly', 1);
// ini_set('session.cookie_secure', 1);

class Session{

    public static function start(){
        if(!isset($_SESSION) || empty($_SESSION))
            session_start();

        if(!isset($_SESSION['CSRF'], $_COOKIE['CSRF']))
            self::gen_csrf_tokens();
    }


    public static function destroy(){
        session_reset();

        $_SESSION = [];
    }


    public static function checkCSRF(string $csrf) {
        $token_private  = $_SESSION['CSRF'];
        $token_public   = $_COOKIE['CSRF'];

        $csrf_decode = base64_decode($csrf);

        $time   = substr($csrf_decode, strpos($csrf_decode, '|') + 1);

        $gen    = base64_encode(md5($token_private . $time . $token_public) . '|' . $time);

        // if($time < time() - 300)
        //     return false;

        return ($csrf == $gen);
    }


    public static function getCSRF(){
        $token_private  = $_SESSION['CSRF'];
        $token_public   = $_COOKIE['CSRF'];

        $csrf = base64_encode(md5($token_private . L_TIME . $token_public) . '|' . L_TIME);

        return $csrf;
    }


    private static function gen_csrf_tokens(): void {
        if(!isset($_SESSION['CSRF']))
            $_SESSION['CSRF'] = self::gen_token();

        if(!isset($_COOKIE['CSRF'])) {
            $_COOKIE['CSRF'] = self::gen_token();

            setcookie('CSRF', $_COOKIE['CSRF'], 0, '/', '', true, true);
        }
    }


    private static function gen_token(){
        if (function_exists('com_create_guid') === true)
            return trim(com_create_guid(), '{}');

        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }

}