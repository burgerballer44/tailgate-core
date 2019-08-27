<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\Team\TeamId;

class SeasonDeleted implements DomainEvent, SeasonDomainEvent
{
    private $seasonId;
    private $occurredOn;

    public function __construct(SeasonId $seasonId)
    {
        $this->seasonId = $seasonId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->seasonId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
