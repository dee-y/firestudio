<?php

namespace Fire\Studio\Application\Module;

use \Fire\Studio\Module;

class ApplicationModule extends Module {

    const TEMPLATE_APPLICATION_LAYOUT = 'fire.studio.standard.layout';
    const PARTIAL_APPLICATION_PARTIAL_HTML_HEAD = 'fire.studio.partial.html.head';
    const URL_LOGIN = 'application.login';

    public function config()
    {
        $this->loadConfig(__DIR__ . '/ApplicationModule/Config/module.json');
    }

    public function run()
    {
        $this->_loadPartials();
        $this->_loadTemplates();
    }

    private function _loadPartials()
    {
        $this->loadPartial(
            self::PARTIAL_APPLICATION_PARTIAL_HTML_HEAD,
            __DIR__ . '/ApplicationModule/Template/partials/htmlHead.phtml'
        );
    }

    private function _loadTemplates()
    {
        $this->loadTemplate(
            self::TEMPLATE_APPLICATION_LAYOUT,
            __DIR__ . '/ApplicationModule/Template/layouts/standard-layout.phtml'
        );
    }

}
