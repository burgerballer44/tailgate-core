<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Domain\Model\Group\GroupId;

class Follow
{
    private $teamId;
    private $followId;
    private $groupId;

    private function __construct($teamId, $followId, $groupId)
    {
        $this->teamId = $teamId;
        $this->followId = $followId;
        $this->groupId = $groupId;
    }

    public static function create(TeamId $teamId, FollowId $followId, GroupId $groupId)
    {
        $newFollow = new Follow($teamId, $followId, $groupId);

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
}
