<?php

namespace Fire\Studio\Application\Plugin;

use \Fire\Studio\Plugin;

class AuthenticationPlugin extends Plugin
{

    public function config()
    {
        $this->loadConfig(__DIR__ . '/AuthenticationPlugin/Config/plugin.json');
    }

}
