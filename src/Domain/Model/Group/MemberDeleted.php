<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\User\UserId;

class MemberDeleted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $memberId;
    private $occurredOn;

    public function __construct(GroupId $groupId, MemberId $memberId)
    {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
