<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\User\UserId;

class MemberUpdated implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $memberId;
    private $groupRole;
    private $occurredOn;

    public function __construct(GroupId $groupId, MemberId $memberId, $groupRole)
    {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
        $this->groupRole = $groupRole;
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

    public function getGroupRole()
    {
        return $this->groupRole;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
