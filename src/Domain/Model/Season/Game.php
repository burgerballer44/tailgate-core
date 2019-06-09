<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\Team\TeamId;

class Game
{
    private $gameId;
    private $seasonId;
    private $homeTeamId;
    private $awayTeamId;
    private $homeTeamScore;
    private $awayTeamScore;
    private $startDate;

    private function __construct(
        $gameId,
        $seasonId,
        $homeTeamId,
        $awayTeamId,
        $startDate
    ) {
        $this->gameId = $gameId;
        $this->seasonId = $seasonId;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->startDate = $startDate;
    }

    public static function create(
        GameId $gameId,
        SeasonId $seasonId,
        TeamId $homeTeamId,
        TeamId $awayTeamId,
        \DateTimeImmutable $startDate
    ) {
        $newGame = new Game(
            $gameId,
            $seasonId,
            $homeTeamId,
            $awayTeamId,
            $startDate
        );

        return $newGame;
    }

    public function getGameId()
    {
        return $this->gameId;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
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

    public function addHomeTeamScore(int $homeTeamScore)
    {
        $this->homeTeamScore = $homeTeamScore;
    }

    public function addAwayTeamScore(int $awayTeamScore)
    {
        $this->awayTeamScore = $awayTeamScore;
    }
}
