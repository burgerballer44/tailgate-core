<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Common\TimeOrString;
use Tailgate\Domain\Model\DomainEvent;

class GameScoreUpdated implements DomainEvent, SeasonDomainEvent
{
    private $seasonId;
    private $gameId;
    private $homeTeamScore;
    private $awayTeamScore;
    private $startDate;
    private $startTime;
    private $dateOccurred;

    public function __construct(
        SeasonId $seasonId,
        GameId $gameId,
        $homeTeamScore,
        $awayTeamScore,
        DateOrString $startDate,
        TimeOrString $startTime,
        Date $dateOccurred
    ) {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->homeTeamScore = $homeTeamScore;
        $this->awayTeamScore = $awayTeamScore;
        $this->startDate = $startDate;
        $this->startTime = $startTime;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Game score updated.';
    }

    public function getAggregateId()
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

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
