<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\DomainEvent;

class GroupDeleted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $dateOccurred;

    public function __construct(GroupId $groupId, $dateOccurred)
    {
        $this->groupId = $groupId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Group deleted.';
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
