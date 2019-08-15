<?php

namespace Tailgate\Application\Command\Team;

use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Model\Team\Team;

class AddTeamHandler
{
    public $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function handle(AddTeamCommand $addTeamCommand)
    {
        $designation = $addTeamCommand->getDesignation();
        $mascot = $addTeamCommand->getMascot();

        $team = Team::create(
            $this->teamRepository->nextIdentity(),
            $designation,
            $mascot
        );
        
        $this->teamRepository->add($team);
    }
}
