<?php

namespace Tailgate\Application\Command\Team;

class FollowTeamCommand
{
    private $groupId;
    private $teamId;
    private $seasonId;

    public function __construct(string $groupId, string $teamId, string $seasonId)
    {
        $this->groupId = $groupId;
        $this->teamId = $teamId;
        $this->seasonId = $seasonId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }
}
