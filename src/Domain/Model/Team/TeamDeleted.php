<?php

namespace Tailgate\Domain\Model\Team;

use Buttercup\Protects\DomainEvent;

class TeamDeleted implements DomainEvent, TeamDomainEvent
{
    private $teamId;
    private $occurredOn;

    public function __construct(TeamId $teamId)
    {
        $this->teamId = $teamId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->teamId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
