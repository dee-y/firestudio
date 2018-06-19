<?php

namespace Fire\Studio\Application\Module\ApplicationModule\Plugin;

use \Fire\Studio\Plugin;
use \Fire\Studio\Application\Module\ApplicationModule\Service\UserAuth;

class AuthenticationPlugin extends Plugin
{

    const INJECTOR_USER = 'fire.studio.user';

    public function config()
    {
        $this->injector()->set(self::INJECTOR_USER, new UserAuth());
    }

}
