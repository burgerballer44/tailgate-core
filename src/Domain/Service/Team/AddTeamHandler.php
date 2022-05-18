<?php

namespace Tailgate\Domain\Service\Team;

use Tailgate\Application\Command\Team\AddTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class AddTeamHandler implements ValidatableService
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

    public function handle(AddTeamCommand $command)
    {
        $this->validate($command);

        $team = Team::create(
            $this->teamRepository->nextIdentity(),
            $command->getDesignation(),
            $command->getMascot(),
            Sport::fromString($command->getSport()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->teamRepository->add($team);
    }
}
