<?php

namespace Tailgate\Application\Command\Group;

class DeleteMemberCommand
{
    private $groupId;
    private $memberId;

    public function __construct($groupId, $memberId)
    {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }
}
