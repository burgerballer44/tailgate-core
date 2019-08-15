<?php

namespace Tailgate\Application\Command\Team;

use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamId;

class FollowTeamHandler
{
    public $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function handle(FollowTeamCommand $followTeamCommand)
    {
        $groupId = $followTeamCommand->getGroupId();
        $teamId = $followTeamCommand->getTeamId();

        $team = $this->teamRepository->get(TeamId::fromString($teamId));

        $team->followTeam(GroupId::fromString($groupId));
        
        $this->teamRepository->add($team);
    }
}
