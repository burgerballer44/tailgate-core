<?php

namespace Tailgate\Application\Command\Group;

class DeletePlayerCommand
{
    private $groupId;
    private $playerId;

    public function __construct($groupId, $playerId)
    {
        $this->groupId = $groupId;
        $this->playerId = $playerId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getPlayerId()
    {
        return $this->playerId;
    }
}
