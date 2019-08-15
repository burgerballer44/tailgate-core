<?php

namespace Tailgate\Application\Query\User;

class UserQuery
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
