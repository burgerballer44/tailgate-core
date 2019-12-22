<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Command\Team\AddTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class AddTeamHandler extends AbstractService
{
    public $teamRepository;

    public function __construct(ValidatorInterface $validator, TeamRepositoryInterface $teamRepository)
    {
        parent::__construct($validator);
        $this->teamRepository = $teamRepository;
    }

    public function handle(AddTeamCommand $command)
    {
        $this->validate($command);
        
        $designation = $command->getDesignation();
        $mascot = $command->getMascot();

        $team = Team::create(
            $this->teamRepository->nextIdentity(),
            $designation,
            $mascot
        );
        
        $this->teamRepository->add($team);
    }
}
