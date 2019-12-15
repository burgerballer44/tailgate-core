<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\DomainEvent;

class SeasonUpdated implements DomainEvent, SeasonDomainEvent
{
    private $seasonId;
    private $sport;
    private $seasonType;
    private $name;
    private $seasonStart;
    private $seasonEnd;
    private $occurredOn;

    public function __construct(
        SeasonId $seasonId,
        $sport,
        $seasonType,
        $name,
        $seasonStart,
        $seasonEnd
    ) {
        $this->seasonId = $seasonId;
        $this->sport = $sport;
        $this->seasonType = $seasonType;
        $this->name = $name;
        $this->seasonStart = $seasonStart;
        $this->seasonEnd = $seasonEnd;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->seasonId;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }

    public function getSport()
    {
        return $this->sport;
    }

    public function getSeasonType()
    {
        return $this->seasonType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSeasonStart()
    {
        return $this->seasonStart;
    }

    public function getSeasonEnd()
    {
        return $this->seasonEnd;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
