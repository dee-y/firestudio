<?php

namespace Fire\Studio;

use \Fire\Studio\BaseComponent;
use \Fire\Studio;
use \Valitron\Validator;

abstract class Controller extends BaseComponent {

    const FIRESTUDIO_PAGE_CONTENT = 'fire.studio.page.content';
    const FIRESTUDIO_PAGE_SIDEBAR = 'fire.studio.page.sidebar';
    const FIELD_VALIDATION_REQUIRED = 'required';

    private $_layoutTemplate;

    public function __construct()
    {
        parent::__construct();
        $this->_layoutTemplate = '';
    }

    public function run() {}

    public function postRun() {}

    public function getFormPost()
    {
        return new Validator((array) $this->getPost());
    }

    public function getFormGet()
    {
        return new Validator((array) $this->getGet());
    }

    public function setSessionErrors($errors)
    {
        $_SESSION['fserrors'] = $errors;
    }

    public function getSessionErrors()
    {
        $errors = $_SESSION['fserrors'];
        $this->clearSessionErrors();
        return isset($errors) ? $errors : false;
    }

    public function clearSessionErrors()
    {
        unset($_SESSION['fserrors']);
    }

    public function setSessionMessage($message)
    {
        $_SESSION['fsmessage'] = $message;
    }

    public function getSessionMessage()
    {
        $message = $_SESSION['fsmessage'];
        $this->clearSessionMessage();
        return isset($message) ? $message : false;
    }

    public function clearSessionMessage()
    {
        unset($_SESSION['fsmessage']);
    }

    public function setLayout($templateId)
    {
        $this->_layoutTemplate = $templateId;
    }

    public function setPageTemplate($pathToTemplate)
    {
        $this->loadPartial(self::FIRESTUDIO_PAGE_CONTENT, $pathToTemplate);
    }

    public function setSidebarTemplate($pathToTemplate)
    {
        $this->loadPartial(self::FIRESTUDIO_PAGE_SIDEBAR, $pathToTemplate);
    }

    public function renderHtml()
    {
        $view = $this->injector()->get(Studio::INJECTOR_VIEW);
        return $view->render($this->_layoutTemplate, $this->model);
    }

    public function renderDebugPanel()
    {
        echo $this->injector()->get(Studio::INJECTOR_DEBUG_PANEL)->render();
    }
}
