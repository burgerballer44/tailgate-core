<?php

namespace Tailgate\Domain\Model\Season;

class GameView
{
    private $seasonId;
    private $gameId;
    private $homeTeamId;
    private $awayTeamId;
    private $homeTeamScore;
    private $awayTeamScore;
    private $startDate;

    private function __construct(
        $seasonId,
        $gameId,
        $homeTeamId,
        $awayTeamId,
        $homeTeamScore,
        $awayTeamScore,
        $startDate
    ) {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->homeTeamScore = $homeTeamScore;
        $this->awayTeamScore = $awayTeamScore;
        $this->startDate = $startDate;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }

    public function getGameId()
    {
        return $this->gameId;
    }

    public function getHomeTeamId()
    {
        return $this->homeTeamId;
    }

    public function getAwayTeamId()
    {
        return $this->awayTeamId;
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

}
