<?php

namespace Tailgate\Application\Command\User;

class ActivateUserCommand
{
    private $userId;

    public function __construct(string $userId)
    {
        $test = true;
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}
