<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Season\SeasonId;

class Follow
{
    private $teamId;
    private $followId;
    private $groupId;
    private $seasonId;

    private function __construct($teamId, $followId, $groupId, $seasonId)
    {
        $this->teamId = $teamId;
        $this->followId = $followId;
        $this->groupId = $groupId;
        $this->seasonId = $seasonId;
    }

    public static function create(TeamId $teamId, FollowId $followId, GroupId $groupId, SeasonId $seasonId)
    {
        $newFollow = new Follow($teamId, $followId, $groupId, $seasonId);

        return $newFollow;
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

    public function getSeasonId()
    {
        return $this->seasonId;
    }
}
