<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\User\UserId;

class PlayerDeleted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $playerId;
    private $occurredOn;

    public function __construct(GroupId $groupId, PlayerId $playerId)
    {
        $this->groupId = $groupId;
        $this->playerId = $playerId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
