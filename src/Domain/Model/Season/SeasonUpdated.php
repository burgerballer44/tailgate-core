<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\DomainEvent;

class SeasonUpdated implements DomainEvent, SeasonDomainEvent
{
    private $seasonId;
    private $sport;
    private $seasonType;
    private $name;
    private $seasonStart;
    private $seasonEnd;
    private $dateOccurred;

    public function __construct(
        SeasonId $seasonId,
        $sport,
        $seasonType,
        $name,
        $seasonStart,
        $seasonEnd,
        $dateOccurred
    ) {
        $this->seasonId = $seasonId;
        $this->sport = $sport;
        $this->seasonType = $seasonType;
        $this->name = $name;
        $this->seasonStart = $seasonStart;
        $this->seasonEnd = $seasonEnd;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Season information updated.';
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

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
