<?php

namespace Fire\Studio\Application\Plugin\AuthenticationPlugin\Controller;

use \Fire\Studio\Controller;

class AuthController extends Controller
{

    public function login()
    {
        echo $this->renderHtml();
        echo $this->renderDebugPanel();
    }

}
