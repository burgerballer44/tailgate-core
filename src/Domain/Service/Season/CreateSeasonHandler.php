<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\CreateSeasonCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonType;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class CreateSeasonHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $clock;
    private $seasonRepository;

    public function __construct(ValidatorInterface $validator, Clock $clock, SeasonRepositoryInterface $seasonRepository)
    {
        $this->validator = $validator;
        $this->clock = $clock;
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(CreateSeasonCommand $command)
    {
        $this->validate($command);

        $season = Season::create(
            $this->seasonRepository->nextIdentity(),
            $command->getName(),
            Sport::fromString($command->getSport()),
            SeasonType::fromString($command->getSeasonType()),
            DateOrString::fromString($command->getSeasonStart()),
            DateOrString::fromString($command->getSeasonEnd()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );
        
        $this->seasonRepository->add($season);
    }
}
