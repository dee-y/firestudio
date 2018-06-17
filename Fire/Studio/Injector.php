<?php

namespace Fire\Studio;

use Fire\Injector as FireInjector;

trait Injector {

    public function injector()
    {
        return FireInjector::instance();
    }

}
