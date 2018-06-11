<?php

namespace Fire\Studio;

use Fire\Injector as FireInjector;

trait Injector {

    public $injector;

    private function _fireInjector() {
        $this->injector = FireInjector::instance();
    }

}
