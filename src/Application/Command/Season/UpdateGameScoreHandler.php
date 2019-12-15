<?php

namespace Tailgate\Application\Command\Season;

use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;

class UpdateGameScoreHandler
{
    public $seasonRepository;

    public function __construct(SeasonRepositoryInterface $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(UpdateGameScoreCommand $command)
    {
        $seasonId = $command->getSeasonId();
        $gameId = $command->getGameId();
        $homeTeamScore = $command->getHomeTeamScore();
        $awayTeamScore = $command->getAwayTeamScore();
        $startDate = $command->getStartDate();
        $startTime = $command->getStartTime();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->updateGameScore(
            GameId::fromString($gameId),
            $homeTeamScore,
            $awayTeamScore,
            $startDate,
            $startTime
        );
        
        $this->seasonRepository->add($season);
    }
}
