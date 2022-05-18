<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\DomainEvent;

class MemberDeleted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $memberId;
    private $dateOccurred;

    public function __construct(GroupId $groupId, MemberId $memberId, $dateOccurred)
    {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Group member removed.';
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
