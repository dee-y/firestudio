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

    public function setSessionMessage($message)
    {
        $_SESSION[Studio::SESSION_MESSAGE_KEY] = $message;
        Studio::$sessionMessage = $message;
    }

    public function getSessionMessage()
    {
        return Studio::$sessionMessage;
    }

    public function setSessionErrors($errors)
    {
        $_SESSION[Studio::SESSION_ERRORS_KEY] = $errors;
        Studio::$sessionErrors = $errors;
    }

    public function getSessionErrors()
    {
        return Studio::$sessionErrors;
    }

    public function setSessionForm($form)
    {
        $_SESSION[Studio::SESSION_FORM_KEY] = $form;
        Studio::$sessionForm = $form;
    }

    public function getSessionForm()
    {
        return Studio::$sessionForm;
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
        exit();
    }
}
