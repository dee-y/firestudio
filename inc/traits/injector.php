<?php

namespace firestudio\traits;

use firestudio\helpers\injector as injectorHelper;

trait injector {

    public $injector;

    private function _initInjector() {
        $this->injector = injectorHelper::instance();
    }

}
