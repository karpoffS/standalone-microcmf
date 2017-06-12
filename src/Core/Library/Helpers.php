<?php

namespace Core\Library;


class Helpers
{

    /**
     * Просклонение числа
     *
     * @param  int|float     $number
     * @param  array $titles
     * @return string
     */
    static public function declOfNum($number, $titles)
    {
        $cases = [2, 0, 1, 1, 1, 2];

        return $number . ' ' . $titles[
			($number % 100 > 4 && $number % 100 < 20) ? 
					2 : $cases[min($number % 10, 5)]];
    }

    /**
     * Получить период суток
     *
     * @return string
     */
    static public function partOfDay()
    {
        $hour = date('G');

        switch (true) {
            case ($hour <= 5) :
                return 'night';
            case ($hour > 5 and $hour <= 11) :
                return 'morning';
            case ($hour > 11 and $hour <= 17) :
                return 'day';
            case ($hour > 17 and $hour <= 23) :
                return 'evening';
        }
    }

    /**
     * Сделать нормальную дату и время
     * пример: 01 января 2013 в 12:00
     *
     * @param  string  $date Дата timestamp
     * @param  boolean $time Вернуть время?
     * @return string
     */
    static public function realDate($date, $time = null)
    {
        if (empty($date)) {
            return false;
        }

        $month_array = [
            '01' => 'января',
            '02' => 'февраля',
            '03' => 'марта',
            '04' => 'апреля',
            '05' => 'мая',
            '06' => 'июня',
            '07' => 'июля',
            '08' => 'августа',
            '09' => 'сентября',
            '10' => 'октября',
            '11' => 'ноября',
            '12' => 'декабря'
        ];

        $date    = explode(' ', $date);
        $date[0] = explode('-', $date[0]);
        if (!empty($date[1])) {
            $date[1] = explode(':', $date[1]);
        }
        
        $str_date = $date[0][2] . ' ' 
				. $month_array[$date[0][1]] . ' ' . $date[0][0] . 
				($time ? ' в ' . $date[1][0] . ':' . $date[1][1] : '');

        return $str_date;
    }

    /**
     * Сгенерировать ссылку из кошелька Payeer
     *
     * @param string $payeer
     *
     * @return string
     */
    public function genPlink(string $payeer)
    {
        $symbols = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];

        $payeer = str_split($payeer);

        unset($payeer[0]);

        $link = '';

        foreach ($payeer as $item) {
            $link .= $symbols[$item];
        }

        return $link;
    }

    /**
     * Сгеренрировать хеш ссылки
     *
     * @param string $type
     * @param string $plink
     * @return string
     */
    public function genUnsubscribeHash(string $type, string $plink)
    {
        return md5($type . ' + ' . $plink . ' = (_!_)');
    }

    /**
     * Получить IP клиента
     *
     * @return string
     */
    static function getIp () {
        return key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)
            ? $_SERVER['HTTP_X_FORWARDED_FOR']
            : (
            key_exists('HTTP_X_REAL_IP', $_SERVER)
                ? $_SERVER['HTTP_X_REAL_IP']
                : $_SERVER['REMOTE_ADDR']
            );
    }
}
