<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\Team\TeamId;

class Game
{
    private $gameId;
    private $seasonId;
    private $homeTeamId;
    private $awayTeamId;
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

    public function getStartDate()
    {
        return $this->startDate;
    }
}
