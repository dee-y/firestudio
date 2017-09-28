<?php

namespace Fire\Studio;

use Fire\Studio\Helper\Injector as InjectorHelper;

trait Injector {

    public $injector;

    private function _initInjector() {
        $this->injector = InjectorHelper::instance();
    }

}
