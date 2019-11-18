<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;

class FollowDeleted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $followId;
    private $occurredOn;

    public function __construct(GroupId $groupId, FollowId $followId)
    {
        $this->groupId = $groupId;
        $this->followId = $followId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->groupId;
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
