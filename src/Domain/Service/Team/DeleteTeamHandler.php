<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Command\Team\DeleteTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class DeleteTeamHandler extends AbstractService
{
    private $teamRepository;

    public function __construct(ValidatorInterface $validator, TeamRepositoryInterface $teamRepository)
    {
        parent::__construct($validator);
        $this->teamRepository = $teamRepository;
    }

    public function handle(DeleteTeamCommand $command)
    {
        // $this->validate($command);
        
        $teamId = $command->getTeamId();

        $team = $this->teamRepository->get(TeamId::fromString($teamId));

        $team->delete();

        $this->teamRepository->add($team);
    }
}
