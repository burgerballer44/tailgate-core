<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;

class GroupDeleted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $occurredOn;

    public function __construct(GroupId $groupId)
    {
        $this->groupId = $groupId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
