<?php

namespace Tailgate\Application\Query\Group;

class GroupByUserQuery
{
    private $userId;
    private $groupId;

    public function __construct($userId, $groupId)
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
