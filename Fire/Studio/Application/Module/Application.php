<?php

namespace Fire\Studio\Application\Module;

use \Fire\Studio\Module;

class Application extends Module {

    const TEMPLATE_APPLICATION_LAYOUT = 'fire.studio.standard.layout';
    const TEMPLATE_APPLICATION_PARTIAL_HTML_HEAD = 'fire.studio.partial.html.head';

    public function init()
    {
        $this->loadConfig(__DIR__ . '/Application/Config/module.json');
        $this->_loadPartials();
        $this->_loadTemplates();
    }

    private function _loadPartials()
    {
        $this->loadTemplate(
            self::TEMPLATE_APPLICATION_PARTIAL_HTML_HEAD,
            __DIR__ . '/Application/Template/partials/htmlHead.phtml',
            true
        );
    }

    private function _loadTemplates()
    {
        $this->loadTemplate(
            self::TEMPLATE_APPLICATION_LAYOUT,
            __DIR__ . '/Application/Template/layouts/standard-layout.phtml'
        );
    }

}
