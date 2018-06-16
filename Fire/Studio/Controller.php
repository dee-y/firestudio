<?php

namespace Fire\Studio;

use \Fire\Studio\BaseModuleController;
use \Fire\Studio;

class Controller extends BaseModuleController {

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

    public function renderHtml()
    {
        $view = $this->injector->get(Studio::INJECTOR_VIEW);
        return $view->render($this->_layoutTemplate, $this->model);
    }
}
