<?php

namespace Fire\Studio\Application\Module\AdminModule\Plugin;

use \Fire\Studio\Application\Module\ApplicationModule\Plugin\AuthenticationPlugin
    as ApplicationModuleAuthenicationPlugin;
use \Fire\Studio\Application\Module\ApplicationModule;
use \Fire\Studio;

class AuthenticationPlugin extends ApplicationModuleAuthenicationPlugin
{

    public function postRoute()
    {
        $config = $this->injector()->get(Studio::INJECTOR_CONFIG)->getConfig();
        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        $user = $this->injector()->get(self::INJECTOR_USER);
        $routeId = $router->getId();
        $currentRoute = $config->routes->{$routeId};
        $accessRequest = isset($currentRoute->access) ? $currentRoute->access : false;
        if ($accessRequest && !$user->hasRoles($accessRequest)) {
            $this->setSessionMessage('You do not have the user rights to view this page.');
            $this->redirect(ApplicationModule::URL_LOGIN);
        }
    }

}
