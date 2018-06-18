<?php

namespace Fire\Studio\Application\Plugin\AuthenticationPlugin\Service;

use \Fire\Studio;
use \Fire\Studio\DataMapper;

class UserAuth
{
    use \Fire\Studio\Injector;

    const COLLECTION_FSUSERS = 'FSUsersCollection';
    const ROLE_WEBSITE_USER = 'website';
    const ROLE_DEVELOPER = 'developer';
    const ROLE_ADMIN = 'admin';

    public $__id;
    public $name;
    public $email;
    public $roles;

    private $_isLoggedIn;

    public function __construct()
    {
        $db = $this->injector()->get(Studio::INJECTOR_DATABASE);
        $usersCollection = $db->collection(self::COLLECTION_FSUSERS);

        $fsSession = isset($_SESSION['fsuserauth']) ? $_SESSION['fsuserauth'] : false;

        if ($fsSession) {
            DataMapper::mapDataToObject($this, $fsSession);
            $this->_isLoggedIn = true;
        } else {
            $this->_isLoggedIn = false;
        }
    }

    public function isLoggedIn()
    {
        return $this->_isLoggedIn;
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
