<?php

namespace Tailgate\Application\Query\Group;

class AllGroupsByUserQuery
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
