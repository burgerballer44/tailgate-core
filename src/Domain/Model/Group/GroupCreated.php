<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\DomainEvent;
use Tailgate\Domain\Model\User\UserId;

class GroupCreated implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $name;
    private $inviteCode;
    private $ownerId;
    private $dateOccurred;

    public function __construct(GroupId $groupId, $name, $inviteCode, UserId $ownerId, $dateOccurred)
    {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->inviteCode = $inviteCode;
        $this->ownerId = $ownerId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription() : string
    {
        return 'Group created.';
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

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
