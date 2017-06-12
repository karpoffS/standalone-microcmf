<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 07.06.17
 * Time: 23:06
 */

namespace App\Controller;

use Core\Common\Controller;


class ErrorsController extends Controller
{


    /**
     * Страница не найдена
     *
     * @Route("/notfound")
     * @Method("GET")
     *
     * @return \Zend\Diactoros\Response\HtmlResponse
     */
    public function error404Action()
    {
        $data = $this->views->render("errors::notfound", [
            'title' => "Страница не найдена"
        ]);

        return $this->responseHtml($data, 404);
    }

}
