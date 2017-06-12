<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 31.05.17
 * Time: 0:13
 */

namespace App\Controller;

use Core\Common\Controller;

use UniversalCache\UniversalCache;

class PageController extends Controller
{

    /**
     * Домашняя страница
     *
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction() {

        $key = $this->genKeyName(__FUNCTION__, __NAMESPACE__);

        /** @var UniversalCache $cacher */
        $cacher = $this->container->get('cacheFront');

        if($page = $cacher->tryGet($key)){

            return $this->responseHtml($page);

        } else {

            $data = [
                'title' => "Главна страница!",
            ];

            $page = $this->views->render("public::index", $data);

            $cacher->set($key, $page, 60);

            return $this->responseHtml($page);
        }

    }

    /**
     * Домашняя страница
     *
     * @Route("/first")
     * @Method("GET")
     */
    public function firstAction() {

        $key = $this->genKeyName(__FUNCTION__, __NAMESPACE__);

        /** @var UniversalCache $cacher */
        $cacher = $this->container->get('cache');

        if($page = $cacher->tryGet($key)){

            return $this->responseHtml($page);

        } else {

            $data = [
                'title' => "Первая страница!",
            ];

            $page = $this->views->render("public::index", $data);

            $cacher->set($key, $page, 60);

            return $this->responseHtml($page);
        }

    }

    /**
     * Домашняя страница
     *
     * @Route("/second")
     * @Method("GET")
     */
    public function secondAction() {

        $key = $this->genKeyName(__FUNCTION__, __NAMESPACE__);

        /** @var UniversalCache $cacher */
        $cacher = $this->container->get('cache');

        if($page = $cacher->tryGet($key)){

            return $this->responseHtml($page);

        } else {

            $data = [
                'title' => "Вторая страница!",
            ];

            $page = $this->views->render("public::index", $data);

            $cacher->set($key, $page, 60);

            return $this->responseHtml($page);
        }

    }

    public function exampleCache(){

        $key = $this->genKeyName(__METHOD__, __NAMESPACE__);

        /** @var UniversalCache $cacher */
        $cacher = $this->container->get('cache');

        if($result = $cacher->tryGet($key)){

            return $result;

        } else {

            // тут бизнес логика
            $result = [];

            $cacher->set($key, $result, 60);

            return $result;
        }

    }

}
