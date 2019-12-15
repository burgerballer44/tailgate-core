<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\Team\TeamId;

class Game
{
    private $seasonId;
    private $gameId;
    private $homeTeamId;
    private $awayTeamId;
    private $homeTeamScore;
    private $awayTeamScore;
    private $startDate;
    private $startTime;

    private function __construct(
        $seasonId,
        $gameId,
        $homeTeamId,
        $awayTeamId,
        $startDate,
        $startTime
    ) {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->startDate = $startDate;
        $this->startTime = $startTime;
    }

    public static function create(
        SeasonId $seasonId,
        GameId $gameId,
        TeamId $homeTeamId,
        TeamId $awayTeamId,
        $startDate,
        $startTime
    ) {
        $newGame = new Game(
            $seasonId,
            $gameId,
            $homeTeamId,
            $awayTeamId,
            $startDate,
            $startTime
        );

        return $newGame;
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

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function addHomeTeamScore(int $homeTeamScore)
    {
        $this->homeTeamScore = $homeTeamScore;
    }

    public function addAwayTeamScore(int $awayTeamScore)
    {
        $this->awayTeamScore = $awayTeamScore;
    }

    public function addStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function addStartTime($startTime)
    {
        $this->startTime = $startTime;
    }
}
