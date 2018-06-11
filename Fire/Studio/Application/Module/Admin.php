<?php

namespace Fire\Studio\Application\Module;

use \Fire\Studio\Module;

class Admin extends Module {

    const TEMPLATE_ADMIN_LAYOUT = 'fire.studio.admin.layout';

    public function init()
    {
        $this->addConfig(__DIR__ . '/Admin/Config/module.json');
        $this->loadTemplate(
            self::TEMPLATE_ADMIN_LAYOUT,
            __DIR__ . '/Admin/Template/layouts/standard-layout.phtml'
        );
    }

}
