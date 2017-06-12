<?php

/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 20.05.17
 * Time: 22:57
 */
namespace Core\Common\Traits;


use League\Container\Container;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

trait ControllerTrait
{

    /**
     * @var \League\Container\Container
     */
    public $container;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    public $request;

    /**
     * @var \League\Plates\Engine
     */
    public $views;

    /**
     * Controller constructor.
     * @param \League\Container\Container $container
     */
    public function __construct(Container &$container)
    {
        $this->container = $container;

        if($container->has('request')){
            $this->request = $container->get('request');
        }

        if($container->has('views')){

            /** @var \League\Plates\Engine views */
            $this->views = $container->get('views');
        }

        $this->onConstruct();
    }

    public function responseHtml($data, $status = 200, array $headers = [])
    {

        str_replace(["\r\n","\n\r","\n","\r","\t"], 'sss', $data);

        return $response = new HtmlResponse($data, $status, $headers);
    }

    public function responseJson($data, $status = 200, array $headers = [], $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Метод вызывающийся во время создания контроллера
     */
    public function onConstruct()
    {
        return;
    }

    /**
     * Возвращает объект параметров
     *
     * @return \Core\Common\Config
     */
    public function getConfig()
    {
        return $this->container->get('config');
    }

    /**
     * Возвращает параметр
     *
     * @param $key
     * @return \stdClass
     */
    public function getParameter($key)
    {
        /** @var \Core\Common\Config $parameters */
        $parameters = $this->container->get('config');

        return $parameters->get($key);
    }

    /**
     * @param string $method
     * @param string $namespace
     * @param bool $short
     * @return string
     */
    public function genKeyName($method, $namespace, $short = false)
    {

        $search = ["Controller", "Model", "Action"];

        $class_name = get_class($this);

        $class_methods = get_class_methods($class_name);
        
        $index = in_array($method, $class_methods) ?: false;

        $class_method = $index ? $class_methods[$index] : md5($class_name);

        $class_name = str_replace($namespace, "", $class_name);

        $class_name = ltrim($class_name, "\\");

        if($short){
            $class_name = str_replace($search, "", $class_name);
            $class_method = str_replace($search, "", $class_method);
        }

        return  $class_name . ":" . $class_method;
    }

}
