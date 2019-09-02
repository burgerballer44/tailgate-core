<?php

namespace Tailgate\Domain\Model\Group;

class PlayerView
{
    private $playerId;
    private $memberId;
    private $groupId;
    private $username;

    public function __construct(
        $playerId,
        $memberId,
        $groupId,
        $username
    ) {
        $this->playerId = $playerId;
        $this->memberId = $memberId;
        $this->groupId = $groupId;
        $this->username = $username;
    }

    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getUsername()
    {
        return $this->username;
    }
}
