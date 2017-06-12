<?php

namespace Core\Library;

class Security
{
    /**
     * Есть ли сессия
     *
     * @param  string $key Ключ массива SESSION
     * @return boolean
     */
    static public function checkSession($key)
    {
        return (key_exists($key, $_SESSION) and !empty($_SESSION[$key]));
    }

    /**
     * Сгенерировать пароль
     *
     * @param string $genFrom
     * @param int    $length
     *
     * @return string
     */
    static public function genPassword(string $genFrom = '', int $length = 6) {

        $genFrom = sha1(!empty($genFrom) ? $genFrom : time());

        return substr(
            preg_replace('/[a-z]/', '', $genFrom),
            0,
            $length
        );
    }

    /**
     * Сгенерировать хэш пароля
     *
     * @param string $pass
     *
     * @return string
     */
    static public function hashPassword(string $pass) {
        return sha1($pass . '|' . Config::$app['passSalt']);
    }

}
