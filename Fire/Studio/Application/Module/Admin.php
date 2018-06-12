<?php

namespace Fire\Studio\Application\Module;

use \Fire\Studio\Module;

class Admin extends Module {

    use \Fire\Studio\Injector;

    const TEMPLATE_ADMIN_LAYOUT = 'fire.studio.admin.layout';

    public function init()
    {
        $this->addConfig(__DIR__ . '/Admin/Config/module.json');
        $this->_fireInjector();
        debugger($this->injector->get('fire.studio.config')->getConfig());
        $this->loadTemplate(
            self::TEMPLATE_ADMIN_LAYOUT,
            __DIR__ . '/Admin/Template/layouts/standard-layout.phtml'
        );
    }

}
