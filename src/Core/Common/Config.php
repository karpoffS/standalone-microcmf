<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 22.05.17
 * Time: 12:36
 */

namespace Core\Common;

use Core\Common\Interfaces\ParametersInterface;

/**
 * Class Config
 * @package SuperTools\Library\Core
 */
class Config implements ParametersInterface
{
    /**
     * @var string
     */
    private $files;

    /**
     * @var string
     */
    private $env = "prod";

    /**
     * @var array
     */
    private $patterns = [
        "params",
        "defaults",
        "routing",
        "config"
    ];

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var bool
     */
    private $split = true;

    /**
     * Config constructor.
     * @param array $files
     * @param string $env
     * @throws \Exception
     */
    public function __construct(array $files, $env = "prod")
    {

        // Установим окружение
        $this->env = $env;

        if(!is_array($files) && empty($files))
            throw new \Exception("Config files cannot be empty");

        // Сохраним пути к файлам
        $this->files = $files;

        // Добавим паттерн для окружения
        $this->addPattern("config_".$this->env);

    }

    /**
     * @return bool
     */
    private function isSplitConfigs()
    {
        return $this->split;
    }

    /**
     * @param bool $split
     * @return $this
     */
    public function splitConfigs(bool $split)
    {
        $this->split = $split;
        return $this;
    }

    /**
     * Установить параметр
     *
     * @param null $key
     * @param null $parameter
     * @param bool $object
     * @return $this
     * @throws \Exception
     */
    public function set($key = null, $parameter = null, $object = true)
    {
        if(empty($key))
            throw new \Exception("Not set key container of parameter");

        if(!is_string($key))
            throw new \Exception("Name must be type string");

        if(empty($parameter))
            throw new \Exception("Not set parameter");

        if($object)
            $this->params[$key] = json_decode(json_encode($parameter), FALSE);
//            $this->params[$key] = $this->convert($parameter);
        else
            $this->params[$key] = $parameter;

        return $this;
    }

    private function convert($parameter)
    {
        $param = new \stdClass();

        foreach ($parameter as $name=>$value){
            $param->$name = count($value) > 1 ? $this->convert($value): $value;
        }

        return $param;
    }

    /**
     * Получить пареметр
     *
     * @param string $key
     * @return array|mixed
     * @throws \Exception
     */
    public function get($key)
    {
        if(!$this->has($key))
            throw new \Exception("This parameter ".$key." not found");

        return $this->params[$key];
    }

    /**
     * Возвращает все параметры
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->params;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * Список контейнеров
     *
     * @return array
     */
    public function keys()
    {
        $result = [];
        $keys = $this->params;

        foreach ($keys as $key => $value) {
            $result[$key] = get_class($value);
        }

        $result = json_decode(json_encode($result), false);

        return $result;
    }

    /**
     * Загрузка параметов
     *
     * @return $this
     */
    private function loadParameters()
    {
        // Массив настроек
        $array = $this->loadConfigToArray();

        foreach ($array as $name => $parameter) {
            $this->set($name, $parameter);
        }

        return $this;
    }

    /**
     * Автовызов объекта при обращении к нему как к функции
     *
     * @return $this
     */
    public function __invoke()
    {
        $filename = APP_CACHE."config.cache";

        if(empty($this->params) && file_exists($filename) )
            $cache = file_get_contents($filename);

        if(!empty($cache)){
            $this->params = unserialize($cache);
        }

        if(empty($cache)){

            $this->loadParameters();

            file_put_contents($filename, serialize($this->params));
        }

        return $this;
    }


    /**
     * Загрузка конфигов в виде массива
     *
     * @return mixed
     * @throws \Exception
     */
    private function loadConfigToArray()
    {
        $array = [];

        $files_pattern = implode("|", $this->getPatterns());

        foreach ($this->files as $file) {
            if(!file_exists($file))
                throw new \Exception("File config not found");

            // Имя ключа по имени файла
            $key = pathinfo($file, PATHINFO_FILENAME);

            if(preg_match('/('.$files_pattern.')/i', $key)){

                // Склеиваем всё в один массив
                if($this->isSplitConfigs()){

                    if(preg_match('/(config|config_*)/i', $key))
                        $key = 'config';

                    // Если не пуста
                    if(!empty($array[$key])){
                        $array[$key] =
                            array_replace_recursive(
                                $array[$key],
                                require($file)
                            );
                    }else {
                        $array[$key] = require($file);
                    }

                } else {

                    $array = array_replace_recursive(
                                $array,
                                require($file)
                            );
                }

            }
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getPatterns()
    {
        return $this->patterns;
    }

    /**
     * @param string $pattern
     * @return $this
     */
    public function addPattern(string $pattern)
    {
        array_push($this->patterns, $pattern);

        arsort($this->patterns);

        return $this;
    }

}
