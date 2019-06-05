<?php

namespace Tailgate\Application\Command\Group;

class AddMemberToGroupCommand
{
    private $groupId;
    private $userId;

    public function __construct(string $groupId, string $userId)
    {
        $this->groupId = $groupId;
        $this->userId = $userId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}