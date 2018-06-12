<?php

namespace Fire\Studio\Application\Module;

use \Fire\Studio\Module;

class Application extends Module {
    use \Fire\Studio\Injector;

    const TEMPLATE_APPLICATION_LAYOUT = 'fire.studio.error.layout';

    public function init()
    {
        $this->addConfig(__DIR__ . '/Application/Config/module.json');
        $this->_fireInjector();
        debugger($this->injector->get('fire.studio.config')->getConfig());
        $this->loadTemplate(
            self::TEMPLATE_APPLICATION_LAYOUT,
            __DIR__ . '/Application/Template/layouts/standard-layout.phtml'
        );
    }

}
