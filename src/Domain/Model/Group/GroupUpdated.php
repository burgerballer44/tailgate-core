<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\DomainEvent;
use Tailgate\Domain\Model\User\UserId;

class GroupUpdated implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $name;
    private $ownerId;
    private $dateOccurred;

    public function __construct(GroupId $groupId, $name, UserId $ownerId, Date $dateOccurred)
    {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->ownerId = $ownerId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Group information updated.';
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

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
