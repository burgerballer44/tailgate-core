<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\DomainEvent;

class GameScoreAdded implements DomainEvent, SeasonDomainEvent
{
    private $gameId;
    private $seasonId;
    private $homeTeamScore;
    private $awayTeamScore;
    private $occurredOn;

    public function __construct(
        GameId $gameId,
        SeasonId $seasonId,
        $homeTeamScore,
        $awayTeamScore
    ) {
        $this->gameId = $gameId;
        $this->seasonId = $seasonId;
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

    public function getSeasonId()
    {
        return $this->seasonId;
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
