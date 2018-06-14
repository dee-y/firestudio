<?php

namespace Fire\Studio\Application\Module;

use \Fire\Studio\Module;
use \Fire\Studio\Application\Module\Admin\MenuItem;
use \Fire\Studio\Application\Module\Application;
use \Fire\Studio;

class Admin extends Module {

    const TEMPLATE_ADMIN_LAYOUT = 'fire.studio.admin.layout';
    const PARTIAL_ADMIN_PAGE = 'fire.studio.admin.page';
    const ADMIN_STANDARD_LAYOUT_STYLE = 'admin.standardStyle';
    const ADMIN_DASHBOARD_URL = 'admin.dashboard';

    private $_router;

    public function init()
    {
        $this->loadConfig(__DIR__ . '/Admin/Config/module.json');
        $this->_router = $this->injector->get(Studio::INJECTOR_ROUTER);
        $this->_loadPartials();
        $this->_loadTemplates();
        $this->_addInlineStyles();
        $this->_addInlineScripts();
        $this->_initViewModel();
    }

    public function run()
    {
        $this->_loadMenu();
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

    private function _addInlineStyles()
    {
        $this->addInlineStyle(
            self::ADMIN_STANDARD_LAYOUT_STYLE,
            __DIR__ . '/Admin/Public/css/standardLayout.css'
        );
    }

    private function _addInlineScripts()
    {

    }

    private function _initViewModel()
    {
        $this->model->adminMenu = [];
        $this->model->adminHomeUrl = $this->_router->getUrl(self::ADMIN_DASHBOARD_URL);
    }

    private function _loadMenu()
    {
        if (!isset($this->model->adminMenu)) {
            $this->model->adminMenu = [];
        }

        //dashboard link
        $this->model->adminMenu[] =  new MenuItem(
            self::ADMIN_DASHBOARD_URL,
            'Dashboard',
            $this->_router->getUrl(self::ADMIN_DASHBOARD_URL)
        );
        debugger($this->model->adminMenu);
    }

}
