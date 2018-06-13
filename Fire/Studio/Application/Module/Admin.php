<?php

namespace Fire\Studio\Application\Module;

use \Fire\Studio\Module;
use \Fire\Studio\Application\Module\Application;

class Admin extends Module {

    const TEMPLATE_ADMIN_LAYOUT = 'fire.studio.admin.layout';

    public function init()
    {
        $this->loadConfig(__DIR__ . '/Admin/Config/module.json');
        $this->_loadPartials();
        $this->_loadTemplates();
    }

    private function _loadPartials()
    {
        $this->loadTemplate(
            Application::TEMPLATE_APPLICATION_PARTIAL_HTML_HEAD,
            __DIR__ . '/Application/Template/partials/htmlHead.phtml',
            true
        );
    }

    private function _loadTemplates()
    {
        $this->loadTemplate(
            self::TEMPLATE_ADMIN_LAYOUT,
            __DIR__ . '/Admin/Template/layouts/standard-layout.phtml'
        );
    }

}
