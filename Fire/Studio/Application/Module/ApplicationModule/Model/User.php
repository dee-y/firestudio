<?php

namespace Fire\Studio\Application\Module\ApplicationModule\Model;

use Fire\Studio\DataMapper;

class User {

    const COLLECTION_FSUSERS = 'FSUsersCollection';

    public $name;
    public $email;
    public $password;
    public $roles;

    public function __construct($data = null)
    {
        $this->name = false;
        $this->email = false;
        $this->password = false;
        $this->roles = [];
        $userData = ($data) ? $data : (object) [];
        DataMapper::mapDataToObject($this, $data);
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }

    public function getData()
    {
        return (object) [
            'id' => $this->__id,
            'name' => $this->name,
            'email' => $this->email
        ];
    }

}
