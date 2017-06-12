<?php

namespace Core\Common;

use Core\Library\Logger;
use \PDO;
use \PDOException;
use \Exception;

class DB
{

    private $pdo = null;
    private $prepare    = null;

    function __construct(\PDO $pdo)
    {

        try {

            $this->pdo = $pdo;

        } catch (PDOException $e) {
            Logger::log($e->getMessage(), 'db');
        }
    }

    /**
     * Сбросить запрос
     */
    private function clearPrepare()
    {
        $this->prepare = null;
    }

    /**
     * Выполнить SQL запрос и вернуть кол-во затронутых строк
     *
     * @param  string $sql Строка SQL запроса
     * @return mixed
     */
    public function exec($sql)
    {
        $exec = $this->pdo->exec($sql);
        if (!$exec) {
            Logger::log($sql, 'db');

            return false;
        }

        return $exec;
    }

    /**
     * Выполнить запрос с транзакцией
     *
     * @param  array $sql
     * @return bool
     */
    public function execTransaction($sql)
    {
        if (!is_array($sql)) {
            return false;
        }

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $this->pdo->beginTransaction();

            foreach ($sql as $v) {
                $this->pdo->exec($v);
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            Logger::log(['ERROR' => $e->getMessage(), 'SQL' => $sql], 'db');

            return false;
        }

        return true;
    }

    /**
     * Подготовить и выполнить SQL запрос
     *
     * @param string $sql
     * @param array  $array
     * @return bool
     */
    public function prepareQuery($sql, $array = [true])
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $this->prepare = $this->pdo->prepare($sql); //Подготовить запрос
            $this->prepare->execute($array);
            return true;
        } catch (Exception $e) {
            $this->clearPrepare();
            Logger::log([$e->getMessage(), $array], 'db');
            return false;
        }
    }

    /**
     * Вставить строку в таблицу
     *
     * @param  string $table Таблица
     * @param  array  $array Входящий массив данных(ключ массива - имя поля)
     * @return boolean
     */
    public function insertRow($table, $array)
    {
        $keys   = implode(',', array_keys($array));
        $values = ':' . implode(',:', array_keys($array));
        $sql    = 'INSERT INTO ' . $table . '(' . $keys . ') VALUES(' . $values . ')';

        return $this->prepareQuery($sql, $array) ? $this->lastInsertId() : false;
    }

    /**
     * Вернуть последний вставленный id строки
     *
     * @return integer
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Обновить данные
     *
     * @param  string $table  Таблица
     * @param  array  $array  Входящий массив данных(ключ массива - имя поля)
     * @param  string $clause Условие
     * @return boolean
     */
    public function updateFieldsByClause($table, $array, $clause = null)
    {
        $clause    = $clause ? ' WHERE ' . $clause : '';
        $setFields = $separator = '';
        $last_key  = end($array);
        $last_key  = key($array);
        foreach ($array as $k => $v) {
            $separator = $k == $last_key ? '' : ', ';
            $setFields .= $k . '=:' . $k . $separator;
        }
        $sql = 'UPDATE ' . $table . ' SET ' . $setFields . $clause;

        return $this->prepareQuery($sql, $array);
    }

    /**
     * Удалить строку/строки из таблицы
     *
     * @param  string $table  Таблица
     * @param  string $clause Условие для удаления
     * @return boolean
     */
    public function deleteRow($table, $clause = null)
    {
        $clause = $clause ? ' WHERE ' . $clause : '';
        $sql    = 'DELETE FROM ' . $table . $clause;

        return $this->prepareQuery($sql);
    }

    /**
     * Выбрать количество строк по условию
     *
     * @param  string $table  Имя таблицы
     * @param  string $clause Условие выборки
     * @param  string $column По какому полю считать количество
     * @return number         Вернуть количество строк удовлетворяющих условию
     */
    public function getCount($table, $clause = '', $column = 'id')
    {
        $clause = $clause ? ' WHERE ' . $clause : '';
        $this->prepareQuery('SELECT COUNT(' . $column . ') AS rows FROM ' . $table . $clause);
        $countRows = $this->prepare->fetch(PDO::FETCH_ASSOC);
        $this->clearPrepare();

        return $countRows['rows'];
    }

    /**
     * Выбрать одну или несколько строк
     *
     * @param  string  $table  Таблица
     * @param  string  $fields Поля для выборки
     * @param  string  $clause Условие выборки
     * @param  boolean $cycle  Одну или несколько строк выбирать
     * @return array           Вернуть одну или несколько строк
     */
    public function getFieldsByClause($table, $fields, $clause = null, $cycle = null)
    {
        $clause = $clause ? ' WHERE ' . $clause : '';
        $this->prepareQuery('SELECT ' . $fields . ' FROM ' . $table . $clause);

        return $this->toArray($cycle);
    }

    /**
     * Вернуть массив
     *
     * @param  boolean $cycle Одну или несколько строк выбрать
     * @return array          Вернуть массив выборки
     */
    public function toArray($cycle = null)
    {
        $data = $cycle ? $this->prepare->fetchAll(PDO::FETCH_ASSOC) : $this->prepare->fetch(PDO::FETCH_ASSOC);
        $this->clearPrepare();

        return $data;
    }
}
