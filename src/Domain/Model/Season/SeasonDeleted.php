<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\DomainEvent;

class SeasonDeleted implements DomainEvent, SeasonDomainEvent
{
    private $seasonId;
    private $dateOccurred;

    public function __construct(SeasonId $seasonId, $dateOccurred)
    {
        $this->seasonId = $seasonId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription() : string
    {
        return 'Season removed.';
    }

    public function getAggregateId()
    {
        return $this->seasonId;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
