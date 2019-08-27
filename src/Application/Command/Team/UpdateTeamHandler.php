<?php

namespace Tailgate\Application\Command\Team;

use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;

class UpdateTeamHandler
{
    public $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function handle(UpdateTeamCommand $command)
    {
        $teamId = $command->getTeamId();
        $designation = $command->getDesignation();
        $mascot = $command->getMascot();

        $team = $this->teamRepository->get(TeamId::fromString($teamId));

        $team->update($designation, $mascot);
        
        $this->teamRepository->add($team);
    }
}
