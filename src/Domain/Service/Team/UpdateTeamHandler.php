<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Command\Team\UpdateTeamCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;

class UpdateTeamHandler
{
    private $clock;
    private $teamRepository;

    public function __construct(Clock $clock, TeamRepositoryInterface $teamRepository)
    {
        $this->clock = $clock;
        $this->teamRepository = $teamRepository;
    }

    public function handle(UpdateTeamCommand $command)
    {
        $team = $this->teamRepository->get(TeamId::fromString($command->getTeamId()));

        $team->update(
            $command->getDesignation(),
            $command->getMascot(),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->teamRepository->add($team);
    }
}
