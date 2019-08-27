<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\User\UserId;

class ScoreDeleted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $scoreId;
    private $occurredOn;

    public function __construct(GroupId $groupId, ScoreId $scoreId)
    {
        $this->groupId = $groupId;
        $this->scoreId = $scoreId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getScoreId()
    {
        return $this->scoreId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
