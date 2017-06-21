<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 21.06.17
 * Time: 12:04
 */

namespace Core\Common;

use League\Container\Container;
use Pux\Mux;

/**
 * Class Routing
 * @package Core\Common
 */
class Routing
{
    /**
     * @var array
     */
    private $default_options = [
        'constructor_args' => [ 'container' ],
    ];

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var object
     */
    protected $routes;

    /**
     * @var Mux
     */
    protected $router;

    /**
     * @var array
     */
    protected $params;

    /**
     * Routing constructor.
     * @param Container $container
     * @param array $params
     */
    public function __construct(Container &$container, $params = [])
    {
        $this->container = $container;

        $this->params = $params;

        $this->router = $container->get("router");

        $this->routes = $container->get("config")->get("routing");
    }

    /**
     * Создание роутинга
     */
    public function create()
    {
        $default = $this->prepareDefaultOptions();

        foreach ($this->routes as $route) {

            // Устанавливаем опцию поумолчанию
            if(empty($route['options']))
                $route['options'] = $default;

            // совмещаем опции
            if (!empty($route['options']) && is_array($route['options']))
                $route['options'] =
                    array_merge_recursive($default, $route['options']);

            // Устанавливаем метод ротинга
            $method = !empty($route['method']) ? $route['method'] : "get";

            // Если есть метод то запускаем
            if(!method_exists($this->router, $method))
                throw new \Exception("This {$method} method routing is not available!");

            $this->router->{$method}(
                $route['pattern'],
                [ $route['controller'],$route['action']], // callback
                $route['options'] // options
            );
        }
    }

    /**
     * Подготавливает опцию поумолчанию
     * @return array
     */
    private function prepareDefaultOptions(){

        $result = [];

        foreach ($this->default_options as $key => $option) {
            $result[$key] = $this->parseKeys($option);
        }

        return $result;
    }

    /**
     * Подготовка массива к передаче параметров
     *
     * @param $options
     * @return array
     */
    private function parseKeys($options)
    {
        $result = [];

        foreach ($options as $option) {

            if ($option === 'container'){
                $result = [ &$this->container ];
            }
        }

        return $result;

    }

}