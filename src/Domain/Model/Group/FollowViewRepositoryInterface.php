<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Season\SeasonId;

interface FollowViewRepositoryInterface
{
    public function get(FollowId $id);
    public function getByGroup(GroupId $groupId);
    public function getAllByTeam(TeamId $teamId);
    public function getAllBySeason(SeasonId $seasonId);
}
