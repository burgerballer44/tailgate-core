<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\DomainEvent;

class PlayerDeleted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $playerId;
    private $dateOccurred;

    public function __construct(GroupId $groupId, PlayerId $playerId, $dateOccurred)
    {
        $this->groupId = $groupId;
        $this->playerId = $playerId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Group player removed.';
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
