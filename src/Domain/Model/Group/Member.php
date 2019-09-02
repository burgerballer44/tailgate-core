<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\User\UserId;

class Member
{
    private $groupId;
    private $memberId;
    private $userId;
    private $allowMultiplePlayers;
    private $groupRole;

    private function __construct(
        $groupId,
        $memberId,
        $userId,
        $groupRole,
        $allowMultiplePlayers
    ) {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
        $this->userId = $userId;
        $this->groupRole = $groupRole;
        $this->allowMultiplePlayers = $allowMultiplePlayers;
    }

    public static function create(
        GroupId $groupId,
        MemberId $memberId,
        UserId $userId,
        $groupRole,
        $allowMultiplePlayers
    ) {
        $newMember = new Member(
            $groupId,
            $memberId,
            $userId,
            $groupRole,
            $allowMultiplePlayers
        );

        return $newMember;
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

    public function getGroupRole()
    {
        return $this->groupRole;
    }

    public function getAllowMultiplePlayers()
    {
        return $this->allowMultiplePlayers;
    }

    public function updateGroupRole(string $groupRole)
    {
        $this->groupRole = $groupRole;
    }

    public function updateAllowMultiplePlayers(string $allowMultiplePlayers)
    {
        $this->allowMultiplePlayers = $allowMultiplePlayers;
    }
}
