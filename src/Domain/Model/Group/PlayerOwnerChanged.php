<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;

class PlayerOwnerChanged implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $playerId;
    private $memberId;
    private $occurredOn;

    public function __construct(
        GroupId $groupId,
        PlayerId $playerId,
        MemberId $memberId
    ) {
        $this->groupId = $groupId;
        $this->playerId = $playerId;
        $this->memberId = $memberId;
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
