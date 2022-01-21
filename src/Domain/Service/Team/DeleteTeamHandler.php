<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Command\Team\DeleteTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;

class DeleteTeamHandler
{
    private $teamRepository;
    private $clock;

    public function __construct(TeamRepositoryInterface $teamRepository, Clock $clock)
    {
        $this->teamRepository = $teamRepository;
        $this->clock = $clock;
    }

    public function handle(DeleteTeamCommand $command)
    {
        $teamId = $command->getTeamId();

        $team = $this->teamRepository->get(TeamId::fromString($teamId));

        $team->delete(Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->teamRepository->add($team);
    }
}
