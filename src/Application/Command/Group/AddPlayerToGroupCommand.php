<?php

namespace Tailgate\Application\Command\Group;

class AddPlayerToGroupCommand
{
    private $groupId;
    private $memberId;
    private $username;

    public function __construct($groupId, $memberId, $username)
    {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
        $this->username = $username;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function getUsername()
    {
        return $this->username;
    }
}
