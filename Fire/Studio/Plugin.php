<?php

namespace Fire\Studio;

use \Fire\Studio\BaseComponent;
use \Fire\Studio;

abstract class Plugin extends BaseComponent
{

    public function loadConfig($pathToConfig)
    {
        $config = $this->injector()->get(Studio::INJECTOR_CONFIG);
        $config->addConfigFile($pathToConfig);
    }

    public function config() {}

    public function preRoute() {}

    public function postRoute() {}

    public function preModule() {}

    public function postModule() {}

    public function preController() {}

    public function postController() {}

}
