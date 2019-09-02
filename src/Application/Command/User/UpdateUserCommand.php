<?php

namespace Tailgate\Application\Command\User;

class UpdateUserCommand
{
    private $userId;
    private $email;
    private $status;
    private $role;

    public function __construct(
        string $userId,
        string $email,
        string $status,
        string $role
    ) {
        $this->userId = $userId;
        $this->email = $email;
        $this->status = $status;
        $this->role = $role;
    }

    public function getUserId()
    {
        return $this->userId;
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
