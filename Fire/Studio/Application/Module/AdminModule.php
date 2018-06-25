<?php

namespace Fire\Studio\Application\Module;

use \Fire\Studio\Module;
use \Fire\Studio\Application\Module\AdminModule\MenuItem;
use \Fire\Studio\Application\Module\ApplicationModule;
use \Fire\Studio;

class AdminModule extends Module {

    const TEMPLATE_ADMIN_LAYOUT = 'fire.studio.admin.layout';
    const PARTIAL_ADMIN_DATA_PANEL = 'fire.studio.admin.partial.adminDataPanel';
    const STYLE_ADMIN_STANDARD_LAYOUT = 'admin.standardStyle';
    const STYLE_ADMIN_DATA_PANEL = 'admin.partial.adminDataPanel';
    const URL_ADMIN_DASHBOARD = 'application.admin.dashboard';
    const URL_ADMIN_USERS = 'application.admin.users';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_DEVELOPER = 'developer';

    public function config()
    {
        $this->loadConfig(__DIR__ . '/AdminModule/Config/module.json');
        $this->loadConfig(__DIR__ . '/AdminModule/Config/collectionUsers.json');
    }

    public function run()
    {
        //load templates
        $this->loadTemplate(
            self::TEMPLATE_ADMIN_LAYOUT,
            __DIR__ . '/AdminModule/Template/layouts/standard-layout.phtml'
        );

        //load partials
        $this->loadPartial(
            ApplicationModule::PARTIAL_APPLICATION_PARTIAL_HTML_HEAD,
            __DIR__ . '/ApplicationModule/Template/partials/htmlHead.phtml'
        );
        $this->loadPartial(
            ApplicationModule::PARTIAL_APPLICATION_PARTIAL_SESSION_ERRORS,
            __DIR__ . '/ApplicationModule/Template/partials/sessionErrors.phtml'
        );
        $this->loadPartial(
            ApplicationModule::PARTIAL_APPLICATION_PARTIAL_SESSION_MESSAGE,
            __DIR__ . '/ApplicationModule/Template/partials/sessionMessage.phtml'
        );
        $this->loadPartial(
            self::PARTIAL_ADMIN_DATA_PANEL,
            __DIR__ . '/AdminModule/Template/partials/adminDataPanel.phtml'
        );

        //load inline styles
        $this->addInlineStyle(
            ApplicationModule::STYLE_APPLICATION_BOOTSTRAP,
            __DIR__ . '/ApplicationModule/Public/css/bootstrap.css'
        );
        $this->addInlineStyle(
            self::STYLE_ADMIN_STANDARD_LAYOUT,
            __DIR__ . '/AdminModule/Public/css/standardLayout.css'
        );
        $this->addInlineStyle(
            self::STYLE_ADMIN_DATA_PANEL,
            __DIR__ . '/AdminModule/Public/css/partials/adminDataPanel.css'
        );

        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        $this->model->adminHomeUrl = $router->getUrl(self::URL_ADMIN_DASHBOARD);

        if (!isset($this->model->adminMenu)) {
            $this->model->adminMenu = [];
        }
        $this->model->adminMenu[] =  new MenuItem('Dashboard', self::URL_ADMIN_DASHBOARD);
        $this->model->adminMenu[] =  new MenuItem('Users', self::URL_ADMIN_USERS);
    }
}
