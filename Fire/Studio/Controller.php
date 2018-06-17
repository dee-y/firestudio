<?php

namespace Fire\Studio;

use \Fire\Studio\BaseComponent;
use \Fire\Studio;

abstract class Controller extends BaseComponent {

    const FIRESTUDIO_PAGE_CONTENT = 'fire.studio.page.content';
    const FIRESTUDIO_PAGE_SIDEBAR = 'fire.studio.page.sidebar';

    private $_layoutTemplate;

    public function __construct()
    {
        parent::__construct();
        $this->_layoutTemplate = '';
    }

    public function init() {}

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
}
