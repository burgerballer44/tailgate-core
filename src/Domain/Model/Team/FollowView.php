<?php

namespace Tailgate\Domain\Model\Team;

class FollowView
{
    private $teamId;
    private $followId;
    private $groupId;

    public function __construct($teamId, $followId, $groupId)
    {
        $this->teamId = $teamId;
        $this->followId = $followId;
        $this->groupId = $groupId;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getFollowId()
    {
        return $this->followId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }
}
