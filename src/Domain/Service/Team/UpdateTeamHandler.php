<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Command\Team\UpdateTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateTeamHandler implements ValidatableService
{
    use Validatable;

    private $validator;
    private $teamRepository;

    public function __construct(ValidatorInterface $validator, TeamRepositoryInterface $teamRepository)
    {
        $this->validator = $validator;
        $this->teamRepository = $teamRepository;
    }

    public function handle(UpdateTeamCommand $command)
    {
        $this->validate($command);
        
        $teamId = $command->getTeamId();
        $designation = $command->getDesignation();
        $mascot = $command->getMascot();

        $team = $this->teamRepository->get(TeamId::fromString($teamId));

        $team->update($designation, $mascot);
        
        $this->teamRepository->add($team);
    }
}
