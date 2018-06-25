<?php

namespace Fire\Studio\Application\Module;

use \Fire\Studio\Module;
use \Fire\Studio\Application\Module\AdminModule\MenuItem;
use \Fire\Studio\Application\Module\ApplicationModule;
use \Fire\Studio\Application\Module\AdminModule\Controller\DynamicCollectionsController;
use \Fire\Studio;

class AdminModule extends Module {

    const TEMPLATE_ADMIN_LAYOUT = 'fire.studio.admin.layout';
    const STYLE_ADMIN_STANDARD_LAYOUT = 'admin.standardStyle';
    const URL_ADMIN_DASHBOARD = 'application.admin.dashboard';
    const URL_DYNAMIC_COLLECTIONS = 'application.admin.dynamicCollections';
    const URL_DYNAMIC_COLLECTIONS_NEW = 'application.admin.dynamicCollections.new';
    const URL_DYNAMIC_COLLECTIONS_VIEW = 'application.admin.dynamicCollections.view';
    const URL_DYNAMIC_COLLECTIONS_EDIT = 'application.admin.dynamicCollections.edit';
    const URL_DYNAMIC_COLLECTIONS_DELETE = 'application.admin.dynamicCollections.delete';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_DEVELOPER = 'developer';

    public function config()
    {
        $this->loadConfig(__DIR__ . '/AdminModule/Config/module.json');
        $this->loadConfig(__DIR__ . '/AdminModule/Config/collectionUsers.json');
        $this->model->adminMenu = [];
    }

    public function load()
    {
        //configure dashboard link
        $this->model->adminMenu[] =  new MenuItem('Dashboard', self::URL_ADMIN_DASHBOARD);

        //get dynamic collections links
        $config = $this->injector()->get(Studio::INJECTOR_CONFIG)->getConfig();
        foreach ($config->collections as $slug => $collection) {
            $this->model->adminMenu[] = new MenuItem(
                $collection->pluralName,
                self::URL_DYNAMIC_COLLECTIONS,
                [
                    DynamicCollectionsController::ROUTE_VARIABLE_COLLECTION_ID => $slug
                ]
            );
        }
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

        //load inline styles
        $this->addInlineStyle(
            ApplicationModule::STYLE_APPLICATION_BOOTSTRAP,
            __DIR__ . '/ApplicationModule/Public/css/bootstrap.css'
        );
        $this->addInlineStyle(
            self::STYLE_ADMIN_STANDARD_LAYOUT,
            __DIR__ . '/AdminModule/Public/css/standard-layout.css'
        );

        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        $this->model->adminHomeUrl = $router->getUrl(self::URL_ADMIN_DASHBOARD);
    }
}
