<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\Team\TeamId;

class GameAdded implements DomainEvent
{
    private $gameId;
    private $seasonId;
    private $homeTeamId;
    private $awayTeamId;
    private $startDate;
    private $occurredOn;

    public function __construct(
        GameId $gameId,
        SeasonId $seasonId,
        TeamId $homeTeamId,
        TeamId $awayTeamId,
        \DateTimeImmutable $startDate
    ) {
        $this->gameId = $gameId;
        $this->seasonId = $seasonId;
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
