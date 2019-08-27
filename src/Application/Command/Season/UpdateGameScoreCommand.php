<?php

namespace Tailgate\Application\Command\Season;

class UpdateGameScoreCommand
{
    private $seasonId;
    private $gameId;
    private $homeTeamScore;
    private $awayTeamScore;

    public function __construct(
        string $seasonId,
        string $gameId,
        int $homeTeamScore,
        int $awayTeamScore
    ) {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->homeTeamScore = $homeTeamScore;
        $this->awayTeamScore = $awayTeamScore;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }

    public function getGameId()
    {
        return $this->gameId;
    }

    public function getHomeTeamScore()
    {
        return $this->homeTeamScore;
    }

    public function getAwayTeamScore()
    {
        return $this->awayTeamScore;
    }
}
