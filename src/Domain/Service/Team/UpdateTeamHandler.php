<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Command\Team\UpdateTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class UpdateTeamHandler extends AbstractService
{
    public $teamRepository;

    public function __construct(ValidatorInterface $validator, TeamRepositoryInterface $teamRepository)
    {
        parent::__construct($validator);
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
