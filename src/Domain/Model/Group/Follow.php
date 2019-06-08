<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Team\TeamId;

class Follow
{
    private $followId;
    private $groupId;
    private $teamId;

    private function __construct($followId, $groupId, $teamId)
    {
        $this->followId = $followId;
        $this->groupId = $groupId;
        $this->teamId = $teamId;
    }

    public static function create(FollowId $followId, GroupId $groupId, TeamId $teamId)
    {
        $newFollow = new Follow($followId, $groupId, $teamId);

        return $newFollow;
    }

    public function getFollowId()
    {
        return $this->followId;
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
