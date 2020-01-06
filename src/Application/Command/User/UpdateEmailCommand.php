<?php

namespace Tailgate\Application\Command\User;

class UpdateEmailCommand
{
    private $userId;
    private $email;

    public function __construct($userId, $email)
    {
        $this->userId = $userId;
        $this->email = $email;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
