<?php

namespace Fire\Studio;

class Plugin {

    use \Fire\Studio\Injector;

    public function __construct()
    {
        $this->_initInjector();
    }

}
