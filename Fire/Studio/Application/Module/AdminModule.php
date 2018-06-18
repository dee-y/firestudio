<?php

namespace Fire\Studio\Application\Module;

use \Fire\Studio\Module;
use \Fire\Studio\Application\Module\AdminModule\MenuItem;
use \Fire\Studio\Application\Module\ApplicationModule;
use \Fire\Studio;

class AdminModule extends Module {

    const TEMPLATE_ADMIN_LAYOUT = 'fire.studio.admin.layout';
    const STYLE_ADMIN_STANDARD_LAYOUT = 'admin.standardStyle';
    const URL_ADMIN_DASHBOARD = 'application.admin.dashboard';

    public function config()
    {
        $this->loadConfig(__DIR__ . '/AdminModule/Config/module.json');
    }

    public function run()
    {
        $this->_loadTemplates();
        $this->_loadPartials();
        $this->_addInlineStyles();
        $this->_addInlineScripts();
        $this->_initViewModel();
    }

    private function _loadTemplates()
    {
        $this->loadTemplate(
            self::TEMPLATE_ADMIN_LAYOUT,
            __DIR__ . '/AdminModule/Template/layouts/standard-layout.phtml'
        );
    }

    private function _loadPartials()
    {
        $this->loadPartial(
            ApplicationModule::TEMPLATE_APPLICATION_PARTIAL_HTML_HEAD,
            __DIR__ . '/ApplicationModule/Template/partials/htmlHead.phtml'
        );
    }

    private function _addInlineStyles()
    {
        $this->addInlineStyle(
            self::STYLE_ADMIN_STANDARD_LAYOUT,
            __DIR__ . '/AdminModule/Public/css/standardLayout.css'
        );
    }

    private function _addInlineScripts()
    {

    }

    private function _initViewModel()
    {
        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        $this->model->adminHomeUrl = $router->getUrl(self::URL_ADMIN_DASHBOARD);

        if (!isset($this->model->adminMenu)) {
            $this->model->adminMenu = [];
        }
        $this->model->adminMenu[] =  new MenuItem('Dashboard', self::URL_ADMIN_DASHBOARD);
    }
}
