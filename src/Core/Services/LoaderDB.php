<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 30.05.17
 * Time: 15:14
 */

namespace SASF\Services;

use SASF\Core\DB;

class LoaderDB
{
    /**
     * @var \SASF\Core\DB
     */
    private static $db = null;

    public static function getInstance(\PDO $pdo)
    {
        if(empty(self::$db))
            self::$db = new DB($pdo);

        return self::$db;
    }

}