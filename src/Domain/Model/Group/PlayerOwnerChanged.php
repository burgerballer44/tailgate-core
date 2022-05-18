<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\DomainEvent;

class PlayerOwnerChanged implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $playerId;
    private $memberId;
    private $dateOccurred;

    public function __construct(
        GroupId $groupId,
        PlayerId $playerId,
        MemberId $memberId,
        $dateOccurred
    ) {
        $this->groupId = $groupId;
        $this->playerId = $playerId;
        $this->memberId = $memberId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Group player has changed owner.';
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

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
