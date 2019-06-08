<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Domain\Model\Group\GroupId;

class Follow
{
    private $followId;
    private $teamId;
    private $groupId;

    private function __construct($followId, $teamId, $groupId)
    {
        $this->followId = $followId;
        $this->teamId = $teamId;
        $this->groupId = $groupId;
    }

    public static function create(FollowId $followId, TeamId $teamId, GroupId $groupId)
    {
        $newFollow = new Follow($followId, $teamId, $groupId);

        return $newFollow;
    }

    public function getFollowId()
    {
        return $this->followId;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }
}
