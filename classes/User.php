<?php

class User extends Base
{
    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;

    public $firstName;

    public $lastName;

    public $username;

    public $password;

    public $confirmPass;

    public $role;

    public $avatar;

    public $status;


    public function getAddresses()
    {
        return Address::findBy( 'userId', $this->getId());
    }

    public function getCarts()
    {

    }

    public static function getTableName()
    {
        return 'users';
    }
}