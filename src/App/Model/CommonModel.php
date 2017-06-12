<?php

namespace App\Model;

use Core\DB;

class CommonModel
{
     /**
     * @var DB
     */
    private $db;

    /**
     * CommentsModel constructor.
     */
    public function __construct(DB $db)
    {

        // Подключаем БД
        $this->db = $db;
    }

}
