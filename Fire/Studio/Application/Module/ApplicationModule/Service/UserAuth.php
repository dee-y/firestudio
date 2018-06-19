<?php

namespace Fire\Studio\Application\Module\ApplicationModule\Service;

use \Fire\Studio;
use \Fire\Studio\DataMapper;
use \Fire\Studio\Application\Module\ApplicationModule\Model\User;

class UserAuth
{
    const SESSION_AUTHENTICATION_KEY = 'fsuserauth';

    use \Fire\Studio\Injector;

    public $user;

    public function __construct()
    {
        $fsSession = isset($_SESSION[self::SESSION_AUTHENTICATION_KEY])
            ? $_SESSION[self::SESSION_AUTHENTICATION_KEY] : (object) [];
        $this->user = new User($fsSession);
    }

    public function isLoggedIn()
    {
        return ($this->user->__id) ? true : false;
    }

    public function getId()
    {
        return isset($this->__id) ? $this->__id : false;
    }

    public function getName()
    {
        return isset($this->name) ? $this->name : '';
    }

    public function getEmail()
    {
        return isset($this->email) ? $this->email : '';
    }

    public function getRoles()
    {
        return isset($this->roles) ? $this->roles : [];
    }

    public function hasRole($roleId)
    {
        return (isset($this->roles)) ? in_array($roleId, $this->roles) : false;
    }
}
