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

    public function getVariables($key = null)
    {
        /**
         * @var \Fire\Studio\Application\Service\Router
         */
        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        return $router->getVariables($key);
    }

    public function getGet($key = null)
    {
        $GET = (!empty($_GET)) ? (object) $_GET : false;
        return ($GET)
            ? ($key && isset($GET->{$key}))
                ? $GET->{$key} : $GET
            : false;
    }

    public function getPost($key = null)
    {
        $POST = (!empty($_POST)) ? (object) $_POST : false;
        return ($POST)
            ? ($key && isset($POST->{$key}))
                ? $POST->{$key} : $POST
            : false;
    }

    public function getPut($key = null)
    {
        $PUT = (!empty($_PUT)) ? (object) $_PUT : false;
        return ($PUT)
            ? ($key && isset($PUT->{$key}))
                ? $PUT->{$key} : $PUT
            : false;
    }

    public function getDelete($key = null)
    {
        $DELETE = (!empty($_DELETE)) ? (object) $_DELETE : false;
        return ($DELETE)
            ? ($key && isset($DELETE->{$key}))
                ? $DELETE->{$key} : $DELETE
            : false;
    }

    public function setResponceCode(int $code)
    {
        http_response_code($code);
    }

    public function getResponseCode()
    {
        return http_response_code();
    }

    public function redirect($urlId, $params = [], $responseCode = 302)
    {
        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        $url = $router->getUrl($urlId, $params);
        header('Location: ' . $url, true, $responseCode);
        exit();
    }

    public function redirectToUrl($url, $responseCode = 302)
    {
        header('Location: ' . $url, true, $responseCode);
        exit();
    }
}
