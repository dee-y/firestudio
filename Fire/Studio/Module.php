<?php

namespace Fire\Studio;

use \Fire\Studio;

class Module {

    use \Fire\Studio\Injector;

    private $_config;
    private $_view;

    public function __construct()
    {
        $this->_fireInjector();
        $this->_config = $this->injector->get(Studio::INJECTOR_CONFIG);
        $this->_view = $this->injector->get(Studio::INJECTOR_VIEW);
    }

    public function init()
    {

    }

    public function addConfig($pathToConfig)
    {
        $jsonConfig = file_get_contents($pathToConfig);
        $this->_config->addJsonConfig($jsonConfig);
    }

    public function loadTemplate($id, $pathToTemplate)
    {
        $this->_view->loadTemplate($id, $pathToTemplate);
    }

}
