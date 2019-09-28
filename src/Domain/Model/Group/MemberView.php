<?php

namespace Tailgate\Domain\Model\Group;

class MemberView
{
    private $memberId;
    private $groupId;
    private $userId;
    private $role;
    private $allowMultiplePlayers;
    private $email;

    public function __construct($memberId, $groupId, $userId, $role, $allowMultiplePlayers, $email)
    {
        $this->memberId = $memberId;
        $this->groupId = $groupId;
        $this->userId = $userId;
        $this->role = $role;
        $this->allowMultiplePlayers = $allowMultiplePlayers;
        $this->email = $email;
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

    public function getAllowMultiplePlayers()
    {
        return $this->allowMultiplePlayers;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
