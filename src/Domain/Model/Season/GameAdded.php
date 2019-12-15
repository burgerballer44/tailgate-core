<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\Team\TeamId;

class GameAdded implements DomainEvent, SeasonDomainEvent
{
    private $seasonId;
    private $gameId;
    private $homeTeamId;
    private $awayTeamId;
    private $startDate;
    private $occurredOn;

    public function __construct(
        SeasonId $seasonId,
        GameId $gameId,
        TeamId $homeTeamId,
        TeamId $awayTeamId,
        $startDate
    ) {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->startDate = $startDate;
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
