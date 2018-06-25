<?php

namespace Fire\Studio;

use \Fire\Studio\BaseComponent;
use \Fire\Studio;
use \Fire\Studio\Form;

abstract class Controller extends BaseComponent
{

    const FIRESTUDIO_PAGE_CONTENT = 'fire.studio.page.content';
    const FIRESTUDIO_PAGE_SIDEBAR = 'fire.studio.page.sidebar';

    private $_layoutTemplateId;

    public function __construct()
    {
        parent::__construct();
        $this->_layoutTemplateId = '';
    }

    public function run() {}

    public function postRun() {}

    public function getFormPost()
    {
        $postData = $this->getPost();
        $this->setSessionForm($postData);
        return ($postData) ? new Form($postData) : false;
    }

    public function getFormGet()
    {
        return new Form($this->getGet());
    }

    public function setLayout($templateId)
    {
        $this->_layoutTemplateId = $templateId;
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
        return $view->render($this->_layoutTemplateId, $this->model);
    }

    public function renderDebugPanel()
    {
        echo $this->injector()->get(Studio::INJECTOR_DEBUG_PANEL)->render();
    }
}
