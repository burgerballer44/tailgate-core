<?php

namespace Tailgate\Domain\Model\Group;

class MemberView
{
    private $memberId;
    private $groupId;
    private $userId;
    private $role;

    public function __construct($memberId, $groupId, $userId, $role)
    {
        $this->memberId = $memberId;
        $this->groupId = $groupId;
        $this->userId = $userId;
        $this->role = $role;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getRole()
    {
        return $this->role;
    }
}