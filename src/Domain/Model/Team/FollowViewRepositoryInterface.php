<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\FollowId;

interface FollowViewRepositoryInterface
{
    public function get(FollowId $id);
    public function getAllByTeam(TeamId $teamId);
    public function getAllByGroup(GroupId $groupId);
}
