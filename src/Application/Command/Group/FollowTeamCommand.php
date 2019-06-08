<?php

namespace Tailgate\Application\Command\Group;

class FollowTeamCommand
{
    private $groupId;
    private $teamId;

    public function __construct(string $groupId, string $teamId)
    {
        $this->groupId = $groupId;
        $this->teamId = $teamId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }
}