<?php

namespace Fire\Studio;

use \Fire\Studio;

class Controller {

    use \Fire\Studio\Injector;

    public $model;
    private $_config;
    private $_view;

    public function __construct()
    {
        $this->_fireInjector();
        $this->_config = $this->injector->get(Studio::INJECTOR_CONFIG);
        $this->_view = $this->injector->get(Studio::INJECTOR_VIEW);
        $this->model = (object) [];
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

    public function getTemplate($id)
    {
        return $this->_view->getTemplate($id);
    }

    public function render($layout)
    {
        return $this->_view->render($layout, $this->model);
    }
}
