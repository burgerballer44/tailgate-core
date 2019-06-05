<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\User\UserId;

class Member
{
    private $memberId;
    private $groupId;
    private $userId;
    private $groupRole;

    private function __construct(
        $memberId,
        $groupId,
        $userId,
        $groupRole
    ) {
        $this->memberId = $memberId;
        $this->groupId = $groupId;
        $this->userId = $userId;
        $this->groupRole = $groupRole;
    }

    public static function create(
        MemberId $memberId,
        GroupId $groupId,
        UserId $userId,
        $groupRole
    ) {
        $newMember = new Member(
            $memberId,
            $groupId,
            $userId,
            $groupRole
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
}
