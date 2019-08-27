<?php

namespace Tailgate\Application\Command\Group;

class UpdateMemberCommand
{
    private $groupId;
    private $memberId;
    private $groupRole;

    public function __construct(string $groupId, string $memberId, string $groupRole)
    {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
        $this->groupRole = $groupRole;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function getGroupRole()
    {
        return $this->groupRole;
    }
}
