<?php

namespace Tailgate\Domain\Model\Team;

use Buttercup\Protects\DomainEvent;

class FollowDeleted implements DomainEvent, TeamDomainEvent
{
    private $teamId;
    private $followId;
    private $occurredOn;

    public function __construct(TeamId $teamId, FollowId $followId)
    {
        $this->teamId = $teamId;
        $this->followId = $followId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->teamId;
    }

    public function getFollowId()
    {
        return $this->followId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
