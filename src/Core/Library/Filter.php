<?php

namespace Core\Library;


class Filter
{
    /**
     * Фильтрация данных
     *
     * @param $params
     *
     * @return array|mixed|string
     */
    static public function filterData($params)
    {
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $params[$k] = self::filterData($v);
            }
        } else {
            $params = preg_replace(
                [
                    '/' . PHP_EOL . '{2,}/',
                    '/ {2,}/',
                    '/\'/',
                    '/\`/',
                    '/ё/',
                    '/Ё/'
                ],
                [
                    PHP_EOL,
                    ' ',
                    '"',
                    '"',
                    'е',
                    'Е'
                ],
                $params
            );

            $params = trim(htmlspecialchars($params));
        }

        return $params;
    }
}
