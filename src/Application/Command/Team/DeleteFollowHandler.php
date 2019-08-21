<?php

namespace Tailgate\Application\Command\Team;

use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\FollowId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;

class DeleteFollowHandler
{
    private $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function handle(DeleteFollowCommand $command)
    {
        $teamId = $command->getTeamId();
        $followId = $command->getFollowId();

        $team = $this->teamRepository->get(TeamId::fromString($teamId));

        $team->deleteFollow(FollowId::fromString($followId));

        $this->teamRepository->add($team);
    }
}
