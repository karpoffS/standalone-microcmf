<?php

namespace Core\Library;

class Validator
{
    /**
     * @var \League\Container\Container
     */
    private static $container;

    /**
     * Validator constructor.
     * @param $container
     */
    public static function setContainer($container)
    {
        self::$container = $container;
    }


    /**
     * Проверить длины значения и на пустоту
     *
     * @param string  $string     Строка для проверки
     * @param integer $min_length Мин длина
     * @param integer $max_length Макс длина
     * @return boolean
     */
    static public function validEmpty(string $string, int $min_length, int $max_length = 0)
    {
        if ($max_length > 0) {
            return (mb_strlen($string, 'UTF-8') < $min_length or mb_strlen($string, 'UTF-8') > $max_length) ? false : true;
        } else {
            return mb_strlen($string, 'UTF-8') < $min_length ? false : true;
        }
    }

    /**
     * Проверка на Integer
     *
     * @param string $string
     * @param bool   $negative
     * @return bool
     */
    static public function validInteger(string $string = '', bool $negative = false)
    {
        if (!preg_match('/^\-?[0-9]+$/', $string)) {
            return false;
        }

        if (!$negative and $string < 0) {
            return false;
        }

        return true;
    }

    /**
     * Проверка на латинские символы
     *
     * @param string $string
     * @return int
     */
    static public function validEng(string $string = '')
    {
        return preg_match('/^[a-zA-Z ]+$/', $string);
    }

    /**
     * Проверка на русские символы
     *
     * @param string $string
     * @return int
     */
    static public function validRus(string $string = '')
    {
        return preg_match('/^[а-яА-Я ]+$/u', $string);
    }

    /**
     * Проверка на русские и латинские символы
     *
     * @param string $string
     * @return int
     */
    static public function validEngRus(string $string = '')
    {
        return preg_match('/^[a-zA-Zа-яА-Я ]+$/u', $string);
    }

    /**
     * Проверить допустимые домены почты
     *
     * @param string $string
     * @return bool
     */
    static public function validEmailUkr(string $string = '')
    {
        if (strpos($string, '@')) {
            $email = explode('@', $string);

            if (count($email) === 2) {

                $domainEnd = explode('.', $email[1]);

                $count = count($domainEnd);

                if ($count > 1) {
                    if (in_array($domainEnd[$count-2] . '.' . $domainEnd[$count-1], self::$container->get("config")->app->emailDomains)) {
                        if (filter_var(implode('@', $email), FILTER_VALIDATE_EMAIL)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Проверка на правильность почты
     *
     * @param string $string
     *
     * @return mixed
     */
    static public function validEmail(string $string = '') {
        return filter_var($string, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Проверка номера кошелька Payeer
     *
     * @param  string $string Строка для проверки
     * @return boolean true or false
     */
    static function validPayeer ($string) {
        return preg_match("/^[P]{1}+[0-9]{7,8}$/i", $string) ? true : false;
    }
}
