<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Command\Team\UpdateTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateTeamHandler implements ValidatableService
{
    use Validatable;

    private $validator;
    private $clock;
    private $teamRepository;

    public function __construct(ValidatorInterface $validator, Clock $clock, TeamRepositoryInterface $teamRepository)
    {
        $this->validator = $validator;
        $this->clock = $clock;
        $this->teamRepository = $teamRepository;
    }

    public function handle(UpdateTeamCommand $command)
    {
        $this->validate($command);

        $team = $this->teamRepository->get(TeamId::fromString($command->getTeamId()));

        $team->update($command->getDesignation(), $command->getMascot(), Date::fromDateTimeImmutable($this->clock->currentTime()));
        
        $this->teamRepository->add($team);
    }
}
