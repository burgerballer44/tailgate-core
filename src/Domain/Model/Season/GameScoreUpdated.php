<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\DomainEvent;

class GameScoreUpdated implements DomainEvent, SeasonDomainEvent
{
    private $seasonId;
    private $gameId;
    private $homeTeamScore;
    private $awayTeamScore;
    private $startDate;
    private $startTime;
    private $occurredOn;

    public function __construct(
        SeasonId $seasonId,
        GameId $gameId,
        $homeTeamScore,
        $awayTeamScore,
        $startDate,
        $startTime
    ) {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->homeTeamScore = $homeTeamScore;
        $this->awayTeamScore = $awayTeamScore;
        $this->startDate = $startDate;
        $this->startTime = $startTime;
        $this->occurredOn = new \DateTimeImmutable();
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
