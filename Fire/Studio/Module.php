<?php

namespace Fire\Studio;

use \Fire\Studio\BaseComponent;
use \Fire\Studio;

abstract class Module extends BaseComponent {

    public function loadConfig($pathToConfig)
    {
        $config = $this->injector()->get(Studio::INJECTOR_CONFIG);
        $config->addConfigFile($pathToConfig);
    }

    public function config() {}

    public function load() {}

    public function run() {}

    public function postRun() {}
}
