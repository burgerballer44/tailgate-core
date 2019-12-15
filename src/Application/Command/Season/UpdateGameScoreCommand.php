<?php

namespace Tailgate\Application\Command\Season;

class UpdateGameScoreCommand
{
    private $seasonId;
    private $gameId;
    private $homeTeamScore;
    private $awayTeamScore;
    private $startDate;
    private $startTime;

    public function __construct(
        string $seasonId,
        string $gameId,
        int $homeTeamScore,
        int $awayTeamScore,
        string $startDate,
        string $startTime
    ) {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->homeTeamScore = $homeTeamScore;
        $this->awayTeamScore = $awayTeamScore;
        $this->startDate = $startDate;
        $this->startTime = $startTime;
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

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }
}
