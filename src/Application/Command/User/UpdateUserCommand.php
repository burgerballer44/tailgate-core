<?php

namespace Tailgate\Application\Command\User;

class UpdateUserCommand
{
    private $userId;
    private $username;
    private $email;
    private $status;
    private $role;

    public function __construct(
        string $userId,
        string $username,
        string $email,
        string $status,
        string $role
    ) {
        $this->userId = $userId;
        $this->username = $username;
        $this->email = $email;
        $this->status = $status;
        $this->role = $role;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getRole()
    {
        return $this->role;
    }
}
