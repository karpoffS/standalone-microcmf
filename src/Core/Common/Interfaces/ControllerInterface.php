<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 21.05.17
 * Time: 13:12
 */

namespace Core\Common\Interfaces;


interface ControllerInterface
{

    public function getContainer();

    public function onConstruct();

    public function getParameters();

    public function getParameter($key);
}