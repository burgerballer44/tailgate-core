<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\AddGameCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Common\TimeOrString;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Service\Clock\Clock;

class AddGameHandler
{
    private $clock;
    private $seasonRepository;

    public function __construct(Clock $clock, SeasonRepositoryInterface $seasonRepository)
    {
        $this->clock = $clock;
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(AddGameCommand $command)
    {
        $season = $this->seasonRepository->get(SeasonId::fromString($command->getSeasonId()));

        $season->addGame(
            TeamId::fromString($command->getHomeTeamId()),
            TeamId::fromString($command->getAwayTeamId()),
            DateOrString::fromString($command->getStartDate()),
            TimeOrString::fromString($command->getStartTime()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->seasonRepository->add($season);
    }
}
