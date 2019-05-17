<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\User\UserId;

class User
{
    private $userId;
    private $username;
    private $password;
    private $email;

    private function __construct($userId, $username, $password, $email)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
    }

    public static function create(UserId $userId, $username, $password, $email)
    {
        $newUser = new User($userId, $username, $password, $email);
        return $newUser;
    }

    public function getId()
    {
        return (string) $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
