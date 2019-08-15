<?php

namespace Tailgate\Application\Command\User;

class UpdatePasswordCommand
{
    private $userId;
    private $password;

    public function __construct(string $userId, string $password)
    {
        $this->userId = $userId;
        $this->password = $password;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
