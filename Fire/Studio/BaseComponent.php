<?php

namespace Fire\Studio;

use \Fire\Studio;

abstract class BaseComponent
{
    use \Fire\Studio\Injector;

    public $model;

    public function __construct()
    {
        $this->model = $this->injector()->get(Studio::INJECTOR_MODEL);
    }

    public function loadTemplate($id, $pathToTemplate)
    {
        $view = $this->injector()->get(Studio::INJECTOR_VIEW);
        $view->loadTemplate($id, $pathToTemplate);
    }

    public function loadPartial($id, $pathToPartial)
    {
        $view = $this->injector()->get(Studio::INJECTOR_VIEW);
        $view->loadPartial($id, $pathToPartial);
    }

    public function addInlineStyle($id, $pathToInlineStyle)
    {
        $view = $this->injector()->get(Studio::INJECTOR_VIEW);
        $view->addInlineStyle($id, $pathToInlineStyle);
    }

    public function addInlineScript($id, $pathToInlineScript)
    {
        $view = $this->injector()->get(Studio::INJECTOR_VIEW);
        $view->addInlineScript($id, $pathToInlineScript);
    }

    public function getGet()
    {
        return (isset($_GET)) ? (object) $_GET : false;
    }

    public function getPost()
    {
        return (isset($_POST)) ? (object) $_POST : false;
    }

    public function getPut()
    {
        return (isset($_PUT)) ? (object) $_PUT : false;
    }

    public function getDelete()
    {
        return (isset($_DELETE)) ? (object) $_DELETE : false;
    }

    public function redirect($urlId, $params = [])
    {
        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        $url = $router->getUrl($urlId, $params);
        header('Location: ' . $url);
    }
}
