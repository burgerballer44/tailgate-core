<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamId;

class FollowTeamHandler
{
    public $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(FollowTeamCommand $command)
    {
        $groupId = $command->getGroupId();
        $seasonId = $command->getSeasonId();
        $teamId = $command->getTeamId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->followTeam(TeamId::fromString($teamId), SeasonId::fromString($seasonId));
        
        $this->groupRepository->add($group);
    }
}
