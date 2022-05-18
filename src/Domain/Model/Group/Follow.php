<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;

class Follow
{
    private $groupId;
    private $followId;
    private $teamId;
    private $seasonId;

    private function __construct($groupId, $followId, $teamId, $seasonId)
    {
        $this->groupId = $groupId;
        $this->followId = $followId;
        $this->teamId = $teamId;
        $this->seasonId = $seasonId;
    }

    public static function create(GroupId $groupId, FollowId $followId, TeamId $teamId, SeasonId $seasonId)
    {
        $newFollow = new Follow($groupId, $followId, $teamId, $seasonId);

        return $newFollow;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getFollowId()
    {
        return $this->followId;
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
