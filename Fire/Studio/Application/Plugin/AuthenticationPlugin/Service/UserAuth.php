<?php

namespace Fire\Studio\Application\Plugin\AuthenticationPlugin\Service;

class UserAuth
{

    public function __construct()
    {
        $this->roles = [];
    }

    public function isLoggedIn()
    {

    }

    public function hasRole($roleId)
    {
        return in_array($roleId, $this->roles);
    }

}
