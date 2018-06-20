<?php

namespace Fire\Studio\Application\Module\ApplicationModule\Plugin;

use \Fire\Studio\Plugin;
use \Fire\Studio\Application\Module\ApplicationModule\Service\UserAuth;
use \Fire\Studio;

class AuthenticationPlugin extends Plugin
{

    const INJECTOR_USER = 'fire.studio.user';

    public function config()
    {
        $this->injector()->set(self::INJECTOR_USER, new UserAuth());
    }

    public function postRoute()
    {
        $config = $this->injector()->get(Studio::INJECTOR_CONFIG)->getConfig();
        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        $user = $this->injector()->get(self::INJECTOR_USER);
        $routeId = $router->getId();
        $currentRoute = $config->routes->{$routeId};
        $accessRequest = isset($currentRoute->access) ? $currentRoute->access : false;
        if ($accessRequest && !$user->hasRoles($accessRequest)) {
            $router->setModule('Fire\Studio\Application\Module\ApplicationModule');
            $router->setController('Fire\Studio\Application\Module\ApplicationModule\Controller\ApplicationController');
            $router->setAction('notAuthorized');
        }
    }

}
