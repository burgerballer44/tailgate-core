<?php

namespace Tailgate\Application\Command\Group;

class UpdateMemberCommand
{
    private $groupId;
    private $memberId;
    private $groupRole;
    private $allowMultiplePlayers;

    public function __construct($groupId, $memberId, $groupRole, $allowMultiplePlayers)
    {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
        $this->groupRole = $groupRole;
        $this->allowMultiplePlayers = $allowMultiplePlayers;
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

    public function getAllowMultiplePlayers()
    {
        return $this->allowMultiplePlayers;
    }
}
