<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\DomainEvent;

class GameScoreUpdated implements DomainEvent, SeasonDomainEvent
{
    private $seasonId;
    private $gameId;
    private $homeTeamScore;
    private $awayTeamScore;
    private $occurredOn;

    public function __construct(
        SeasonId $seasonId,
        GameId $gameId,
        $homeTeamScore,
        $awayTeamScore
    ) {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->homeTeamScore = $homeTeamScore;
        $this->awayTeamScore = $awayTeamScore;
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}