<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\Follow;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamId;

class FollowTeamHandler
{
    public $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(FollowTeamCommand $followTeamCommand)
    {
        $groupId = $followTeamCommand->getGroupId();
        $teamId = $followTeamCommand->getTeamId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->followTeam(
            GroupId::fromString($groupId),
            TeamId::fromString($teamId)
        );
        
        $this->groupRepository->add($group);
    }
}