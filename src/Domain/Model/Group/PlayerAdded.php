<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\DomainEvent;

class PlayerAdded implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $playerId;
    private $memberId;
    private $username;
    private $dateOccurred;

    public function __construct(
        GroupId $groupId,
        PlayerId $playerId,
        MemberId $memberId,
        $username,
        Date $dateOccurred
    ) {
        $this->groupId = $groupId;
        $this->playerId = $playerId;
        $this->memberId = $memberId;
        $this->username = $username;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Group player added.';
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
