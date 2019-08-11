<?php

namespace Tailgate\Application\Command\User;

class ActivateUserCommand
{
    private $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}