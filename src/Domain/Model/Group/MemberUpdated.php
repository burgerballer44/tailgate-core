<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\DomainEvent;

class MemberUpdated implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $memberId;
    private $groupRole;
    private $allowMultiplePlayers;
    private $dateOccurred;

    public function __construct(GroupId $groupId, MemberId $memberId, $groupRole, $allowMultiplePlayers, $dateOccurred)
    {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
        $this->groupRole = $groupRole;
        $this->allowMultiplePlayers = $allowMultiplePlayers;
        $this->dateOccurred = $dateOccurred;
    }
    public function getEventDescription() : string
    {
        return 'Group member information updated.';
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

    public function getAllowMultiplePlayers()
    {
        return $this->allowMultiplePlayers;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
