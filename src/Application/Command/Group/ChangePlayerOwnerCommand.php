<?php

namespace Tailgate\Application\Command\Group;

class ChangePlayerOwnerCommand
{
    private $groupId;
    private $playerId;
    private $memberId;

    public function __construct($groupId, $playerId, $memberId)
    {
        $this->groupId = $groupId;
        $this->playerId = $playerId;
        $this->memberId = $memberId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }
}
