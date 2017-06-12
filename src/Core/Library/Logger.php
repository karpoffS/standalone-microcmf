<?php

namespace Core\Library;


class Logger
{
    /**
     * Запись логов
     *
     * @param string|array $log
     * @param string       $name
     */
    static public function log($log, $name = 'default', $ext = 'log')
    {
        $file = implode(".", [$name, $ext]);

        $path = APP_PATH . 'logs/' . $file;

        $fd = fopen($path, "a");

        if ($fd) {

            // Header
            $str = self::calcLengthString("[" . date("Y-m-d H:i:s") . "] ", "-").PHP_EOL;

            // Message
            $str .=  ((is_array($log) or is_object($log)) ? PHP_EOL . print_r($log, true) : $log . PHP_EOL);

            // Delimiter
            $str .= self::calcLengthString("-", "-");

            fwrite($fd, PHP_EOL . $str . PHP_EOL);
            fclose($fd);
        }
    }

    /**
     * Расчитывает длину строки
     *
     * @param $text
     * @param string $symbol
     * @param int $width
     * @return string
     */
    private static function calcLengthString($text, $symbol = " ", $width = 120 )
    {
        return str_pad($text , $width, $symbol, STR_PAD_RIGHT);
    }
}
