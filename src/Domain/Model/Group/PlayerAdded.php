<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;

class PlayerAdded implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $playerId;
    private $memberId;
    private $username;
    private $occurredOn;

    public function __construct(
        GroupId $groupId,
        PlayerId $playerId,
        MemberId $memberId,
        $username
    ) {
        $this->groupId = $groupId;
        $this->playerId = $playerId;
        $this->memberId = $memberId;
        $this->username = $username;
        $this->occurredOn = new \DateTimeImmutable();
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
