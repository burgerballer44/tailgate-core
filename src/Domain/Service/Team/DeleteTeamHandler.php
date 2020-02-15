<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Command\Team\DeleteTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;

class DeleteTeamHandler
{
    private $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function handle(DeleteTeamCommand $command)
    {
        $teamId = $command->getTeamId();

        $team = $this->teamRepository->get(TeamId::fromString($teamId));

        $team->delete();

        $this->teamRepository->add($team);
    }
}
