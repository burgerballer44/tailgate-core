<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\User\UserId;

class GroupCreated implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $name;
    private $inviteCode;
    private $ownerId;
    private $occurredOn;

    public function __construct(GroupId $groupId, $name, $inviteCode, UserId $ownerId)
    {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->inviteCode = $inviteCode;
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

    public function getInviteCode()
    {
        return $this->inviteCode;
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
