<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\User\UserId;

class MemberAdded implements DomainEvent, GroupDomainEvent
{
    private $memberId;
    private $groupId;
    private $userId;
    private $groupRole;
    private $occurredOn;

    public function __construct(
        GroupId $groupId,
        MemberId $memberId,
        UserId $userId,
        $groupRole
    ) {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
        $this->userId = $userId;
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

    public function getUserId()
    {
        return $this->userId;
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
