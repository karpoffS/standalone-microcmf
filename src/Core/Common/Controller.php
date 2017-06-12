<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 12.06.17
 * Time: 0:38
 */

namespace Core\Common;

use Pux\Controller as PuxController;
use Core\Common\Traits\ControllerTrait;

class Controller extends PuxController
{
    use ControllerTrait;
}