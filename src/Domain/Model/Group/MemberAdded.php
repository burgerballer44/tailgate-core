<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\DomainEvent;
use Tailgate\Domain\Model\User\UserId;

class MemberAdded implements DomainEvent, GroupDomainEvent
{
    private $memberId;
    private $groupId;
    private $userId;
    private $groupRole;
    private $allowMultiplePlayers;
    private $dateOccurred;

    public function __construct(
        GroupId $groupId,
        MemberId $memberId,
        UserId $userId,
        $groupRole,
        $allowMultiplePlayers,
        $dateOccurred
    ) {
        $this->groupId = $groupId;
        $this->memberId = $memberId;
        $this->userId = $userId;
        $this->groupRole = $groupRole;
        $this->allowMultiplePlayers = $allowMultiplePlayers;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Group added a member.';
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

    public function getAllowMultiplePlayers()
    {
        return $this->allowMultiplePlayers;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
