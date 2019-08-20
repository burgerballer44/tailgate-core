<?php

namespace Tailgate\Application\Command\Season;

use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;

class AddGameScoreHandler
{
    public $seasonRepository;

    public function __construct(SeasonRepositoryInterface $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(AddGameScoreCommand $command)
    {
        $seasonId = $command->getSeasonId();
        $gameId = $command->getGameId();
        $homeTeamScore = $command->getHomeTeamScore();
        $awayTeamScore = $command->getAwayTeamScore();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->addGameScore(
            GameId::fromString($gameId),
            $homeTeamScore,
            $awayTeamScore
        );
        
        $this->seasonRepository->add($season);
    }
}
