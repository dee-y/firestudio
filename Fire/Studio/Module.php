<?php

namespace Fire\Studio;

use \Fire\Studio;

class Module {

    use \Fire\Studio\Injector;

    protected $_config;
    protected $_view;
    public $model;

    public function __construct()
    {
        $this->_fireInjector();
        $this->_config = $this->injector->get(Studio::INJECTOR_CONFIG);
        $this->_view = $this->injector->get(Studio::INJECTOR_VIEW);
        $this->model = $this->injector->get(Studio::INJECTOR_MODEL);
    }

    public function config()
    {

    }

    public function init()
    {

    }

    public function loadConfig($pathToConfig)
    {
        $this->_config->addConfigFile($pathToConfig);
    }

    public function loadTemplate($id, $pathToTemplate, $loadAsPartial = false)
    {
        $this->_view->loadTemplate($id, $pathToTemplate, $loadAsPartial);
    }

    public function loadPartial($id, $pathToPartial)
    {
        $this->_view->loadTemplate($id, $pathToPartial, true);
    }

    public function addInlineStyle($id, $pathToInlineStyle)
    {
        $this->_view->addInlineStyle($id, $pathToInlineStyle);
    }

    public function addInlineScript($id, $pathToInlineScript)
    {
        $this->_view->addInlineScript($id, $pathToInlineScript);
    }

}
