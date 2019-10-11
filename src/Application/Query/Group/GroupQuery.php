<?php

namespace Tailgate\Application\Query\Group;

class GroupQuery
{
    private $userId;
    private $groupId;

    public function __construct(string $userId, string $groupId)
    {
        $this->userId = $userId;
        $this->groupId = $groupId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }
}
