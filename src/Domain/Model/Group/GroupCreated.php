<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\User\UserId;

class GroupCreated implements DomainEvent
{
    private $groupId;
    private $name;
    private $ownerId;
    private $occurredOn;

    public function __construct(GroupId $groupId, $name, UserId $ownerId)
    {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->ownerId = $ownerId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOwnerId()
    {
        return $this->ownerId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}