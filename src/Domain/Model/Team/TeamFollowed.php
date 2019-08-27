<?php

namespace Tailgate\Domain\Model\Team;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\Group\GroupId;

class TeamFollowed implements DomainEvent, TeamDomainEvent
{
    private $teamId;
    private $followId;
    private $groupId;
    private $occurredOn;

    public function __construct(
        TeamId $teamId,
        FollowId $followId,
        GroupId $groupId
    ) {
        $this->teamId = $teamId;
        $this->followId = $followId;
        $this->groupId = $groupId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->teamId;
    }

    public function getFollowId()
    {
        return $this->followId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
