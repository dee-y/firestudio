<?php

namespace firestudio;

class plugin {

    use \firestudio\traits\injector;

    public function __construct() {
        $this->_initInjector();
    }
    
}
