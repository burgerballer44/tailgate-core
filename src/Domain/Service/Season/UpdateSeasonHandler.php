<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\UpdateSeasonCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonType;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Service\Clock\Clock;

class UpdateSeasonHandler
{
    private $clock;
    private $seasonRepository;

    public function __construct(Clock $clock, SeasonRepositoryInterface $seasonRepository)
    {
        $this->clock = $clock;
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(UpdateSeasonCommand $command)
    {
        $season = $this->seasonRepository->get(SeasonId::fromString($command->getSeasonId()));

        $season->update(
            Sport::fromString($command->getSport()),
            SeasonType::fromString($command->getSeasonType()),
            $command->getName(),
            DateOrString::fromString($command->getSeasonStart()),
            DateOrString::fromString($command->getSeasonEnd()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->seasonRepository->add($season);
    }
}
