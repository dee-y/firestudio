<?php

namespace Fire\Studio\Application\Module\ApplicationModule\Service;

use \Fire\Studio;
use \Fire\Studio\DataMapper;
use \Fire\Studio\Application\Model\User;

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
        return isset($this->user->id) ? $this->user->id : false;
    }

    public function getName()
    {
        return isset($this->user->name) ? $this->user->name : '';
    }

    public function getEmail()
    {
        return isset($this->user->email) ? $this->user->email : '';
    }

    public function getRoles()
    {
        return isset($this->user->roles) ? $this->user->roles : [];
    }

    public function hasRole($roleId)
    {
        return (isset($this->user->roles)) ? in_array($roleId, $this->user->roles) : false;
    }

    public function hasRoles($roles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }
}
