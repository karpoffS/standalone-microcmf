<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 22.05.17
 * Time: 12:31
 */

namespace Core\Common\Interfaces;

/**
 * Interface ParametersInterface
 * @package SASF\Core
 */
interface ParametersInterface
{
    /**
     * Set parameter
     *
     * @param null $key
     * @param null $parameter
     * @param bool $object
     * @return mixed
     */
    public function set($key = null, $parameter = null, $object = true);

    /**
     * Get Parameter
     *
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * Exist parameter key
     *
     * @param $key
     * @return mixed
     */
    public function has($key);

    /**
     * List params on container
     *
     * @return mixed
     */
    public function keys();

}