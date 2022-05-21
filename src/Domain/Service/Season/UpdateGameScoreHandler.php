<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\UpdateGameScoreCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Common\TimeOrString;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;

class UpdateGameScoreHandler
{
    private $clock;
    private $seasonRepository;

    public function __construct(Clock $clock, SeasonRepositoryInterface $seasonRepository)
    {
        $this->clock = $clock;
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(UpdateGameScoreCommand $command)
    {
        $season = $this->seasonRepository->get(SeasonId::fromString($command->getSeasonId()));

        $season->updateGameScore(
            GameId::fromString($command->getGameId()),
            $command->getHomeTeamScore(),
            $command->getAwayTeamScore(),
            DateOrString::fromString($command->getStartDate()),
            TimeOrString::fromString($command->getStartTime()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->seasonRepository->add($season);
    }
}
