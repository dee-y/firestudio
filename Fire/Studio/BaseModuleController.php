<?php

namespace Fire\Studio;

use Fire\Studio;

abstract class BaseModuleController
{
    use \Fire\Studio\Injector;

    public $model;

    public function __construct()
    {
        $this->_fireInjector();
        $this->model = $this->injector->get(Studio::INJECTOR_MODEL);
    }

    public function loadConfig($pathToConfig)
    {
        $config = $this->injector->get(Studio::INJECTOR_CONFIG);
        $config->addConfigFile($pathToConfig);
    }

    public function loadTemplate($id, $pathToTemplate)
    {
        $view = $this->injector->get(Studio::INJECTOR_VIEW);
        $view->loadTemplate($id, $pathToTemplate);
    }

    public function loadPartial($id, $pathToPartial)
    {
        $view = $this->injector->get(Studio::INJECTOR_VIEW);
        $view->loadPartial($id, $pathToPartial);
    }

    public function addInlineStyle($id, $pathToInlineStyle)
    {
        $view = $this->injector->get(Studio::INJECTOR_VIEW);
        $view->addInlineStyle($id, $pathToInlineStyle);
    }

    public function addInlineScript($id, $pathToInlineScript)
    {
        $view = $this->injector->get(Studio::INJECTOR_VIEW);
        $view->addInlineScript($id, $pathToInlineScript);
    }

    public function getTemplate($id)
    {
        $view = $this->injector->get(Studio::INJECTOR_VIEW);
        return $view->getTemplate($id);
    }

    public function getPartial($id)
    {
        $view = $this->injector->get(Studio::INJECTOR_VIEW);
        return $view->getPartial($id);
    }
}
